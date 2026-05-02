<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private function tenantQuery(): Builder
    {
        $q = Client::query();
        $tenantId = auth()->user()->tenantId();
        if ($tenantId) {
            $q->where('tenant_id', $tenantId);
        }
        return $q;
    }

    public function index(Request $request)
    {
        $this->authorize('clients.view');

        $query = $this->tenantQuery()->withCount('events', 'notifications');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('clients.index', compact('clients', 'search'));
    }

    public function create()
    {
        $this->authorize('clients.create');
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $this->authorize('clients.create');
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:2000',
            'next_visit_date'    => 'nullable|date',
            'visit_reminder_days'=> 'nullable|string',
        ]);

        $data['visit_reminder_days'] = $this->parseReminderDays(
            $request->input('visit_reminder_days', '')
        );
        $data['tenant_id'] = auth()->user()->tenantId();

        $client = Client::create($data);

        foreach ($request->input('events', []) as $evt) {
            if (empty($evt['type']) || empty($evt['event_date'])) {
                continue;
            }

            $recurrence = in_array($evt['type'], ['birthday', 'anniversary'])
                ? 'annual'
                : ($evt['recurrence'] ?? 'none');

            $client->events()->create([
                'type'          => $evt['type'],
                'label'         => $evt['label'] ?? null,
                'event_date'    => $evt['event_date'],
                'recurrence'    => $recurrence,
                'reminder_days' => $this->parseReminderDays($evt['reminder_days'] ?? ''),
            ]);
        }

        return redirect()->route('clients.index')
            ->with('success', 'Client added successfully.');
    }

    public function show(Client $client)
    {
        $this->authorize('clients.view');
        $this->authorizeTenant($client);

        $client->load([
            'events',
            'notifications' => function ($q) {
                $q->latest()->take(10);
            },
            'interactions' => function ($q) {
                $q->with('notification')->latest('contacted_at');
            },
        ]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        $this->authorize('clients.edit');
        $this->authorizeTenant($client);
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $this->authorize('clients.edit');
        $this->authorizeTenant($client);

        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:2000',
            'next_visit_date'    => 'nullable|date',
            'visit_reminder_days'=> 'nullable|string',
        ]);

        $data['visit_reminder_days'] = $this->parseReminderDays(
            $request->input('visit_reminder_days', '')
        );

        $client->update($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Client updated.');
    }

    public function destroy(Client $client)
    {
        $this->authorize('clients.delete');
        $this->authorizeTenant($client);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client removed.');
    }

    private function authorizeTenant(Client $client): void
    {
        $tenantId = auth()->user()->tenantId();
        if ($tenantId && $client->tenant_id !== $tenantId) {
            abort(403);
        }
    }

    private function parseReminderDays(string $input): array
    {
        if (empty(trim($input))) {
            return [];
        }

        return array_values(array_filter(
            array_map('intval', preg_split('/[\s,]+/', trim($input))),
            fn ($v) => $v > 0
        ));
    }
}
