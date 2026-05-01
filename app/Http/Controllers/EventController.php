<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function create(Client $client)
    {
        return view('events.create', compact('client'));
    }

    public function store(Request $request, Client $client)
    {
        $data = $request->validate([
            'type'          => 'required|in:birthday,anniversary,visit,custom',
            'label'         => 'nullable|string|max:100',
            'event_date'    => 'required|date',
            'recurrence'    => 'nullable|in:none,weekly,biweekly,monthly,annual',
            'reminder_days' => 'nullable|string',
        ]);

        $data['client_id']     = $client->id;
        $data['recurrence']    = in_array($data['type'], ['birthday', 'anniversary'])
            ? 'annual'
            : ($request->input('recurrence', 'none'));
        $data['reminder_days'] = $this->parseReminderDays(
            $request->input('reminder_days', '')
        );

        Event::create($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Event added.');
    }

    public function edit(Client $client, Event $event)
    {
        abort_if($event->client_id !== $client->id, 404);

        return view('events.edit', compact('client', 'event'));
    }

    public function update(Request $request, Client $client, Event $event)
    {
        abort_if($event->client_id !== $client->id, 404);

        $data = $request->validate([
            'type'          => 'required|in:birthday,anniversary,visit,custom',
            'label'         => 'nullable|string|max:100',
            'event_date'    => 'required|date',
            'recurrence'    => 'nullable|in:none,weekly,biweekly,monthly,annual',
            'reminder_days' => 'nullable|string',
        ]);

        $data['recurrence']    = in_array($data['type'], ['birthday', 'anniversary'])
            ? 'annual'
            : ($request->input('recurrence', 'none'));
        $data['reminder_days'] = $this->parseReminderDays(
            $request->input('reminder_days', '')
        );

        $event->update($data);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Event updated.');
    }

    public function destroy(Client $client, Event $event)
    {
        abort_if($event->client_id !== $client->id, 404);
        $event->delete();

        return redirect()->route('clients.show', $client)
            ->with('success', 'Event removed.');
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
