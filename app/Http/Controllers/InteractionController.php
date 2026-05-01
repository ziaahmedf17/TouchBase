<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\Request;

class InteractionController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id'       => 'required|exists:clients,id',
            'notification_id' => 'nullable|exists:notifications,id',
            'type'            => 'required|in:call,whatsapp,email,visit,other',
            'status'          => 'required|in:reached_out,no_response,responded,follow_up_needed',
            'notes'           => 'nullable|string|max:2000',
            'contacted_at'    => 'required|date',
        ]);

        Interaction::create($data);

        return back()->with('success', 'Interaction logged successfully.');
    }

    public function update(Request $request, Interaction $interaction)
    {
        $data = $request->validate([
            'status'         => 'required|in:reached_out,no_response,responded,follow_up_needed',
            'response_notes' => 'nullable|string|max:2000',
            'response_at'    => 'nullable|date',
        ]);

        $interaction->update($data);

        return back()->with('success', 'Response updated.');
    }

    public function destroy(Interaction $interaction)
    {
        $interaction->delete();

        return back()->with('success', 'Interaction deleted.');
    }
}
