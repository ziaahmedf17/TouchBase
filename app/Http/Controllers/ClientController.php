<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('events', 'notifications');

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
        return view('clients.create');
    }

    public function store(Request $request)
    {
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
        $client->load(['events', 'notifications' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
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
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client removed.');
    }

    /** Parse "1,2,7" or "1 2 7" into [1, 2, 7] */
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
