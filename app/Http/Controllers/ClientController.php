<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Interaction;
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

        $query  = $this->tenantQuery()->withCount('events', 'notifications');
        $search = $request->input('search');
        $visit  = $request->input('visit');
        $gender = $request->input('gender');
        $sort   = $request->input('sort', 'name');
        $dir    = $request->input('dir', 'asc') === 'desc' ? 'desc' : 'asc';

        if (!in_array($sort, ['name', 'next_visit_date'])) {
            $sort = 'name';
        }

        // Subquery: last interaction date per client
        $query->addSelect([
            'last_contacted_at' => Interaction::select('contacted_at')
                ->whereColumn('client_id', 'clients.id')
                ->latest('contacted_at')
                ->limit(1),
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        match ($visit) {
            'week'    => $query->whereNotNull('next_visit_date')
                               ->whereDate('next_visit_date', '>=', now())
                               ->whereDate('next_visit_date', '<=', now()->addDays(7)),
            'month'   => $query->whereNotNull('next_visit_date')
                               ->whereDate('next_visit_date', '>=', now())
                               ->whereDate('next_visit_date', '<=', now()->addDays(30)),
            'overdue' => $query->whereNotNull('next_visit_date')
                               ->whereDate('next_visit_date', '<', now()),
            default   => null,
        };

        if ($gender) {
            $query->where('gender', $gender);
        }

        // Sort — nulls always last for date column
        if ($sort === 'next_visit_date') {
            $query->orderByRaw("(next_visit_date IS NULL) ASC, next_visit_date {$dir}");
        } else {
            $query->orderBy($sort, $dir);
        }

        $clients = $query->paginate(20)->withQueryString();

        return view('clients.index', compact('clients', 'search', 'visit', 'gender', 'sort', 'dir'));
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
            'gender'             => 'nullable|in:male,female,other',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:2000',
            'next_visit_date'    => 'nullable|date',
            'visit_reminder_days'=> 'nullable|string',
        ]);

        $data['visit_reminder_days'] = $this->parseReminderDays($request->input('visit_reminder_days', ''));
        $data['tenant_id']           = auth()->user()->tenantId();

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
            'gender'             => 'nullable|in:male,female,other',
            'phone'              => 'nullable|string|max:30',
            'address'            => 'nullable|string|max:500',
            'notes'              => 'nullable|string|max:2000',
            'next_visit_date'    => 'nullable|date',
            'visit_reminder_days'=> 'nullable|string',
        ]);

        $data['visit_reminder_days'] = $this->parseReminderDays($request->input('visit_reminder_days', ''));

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
