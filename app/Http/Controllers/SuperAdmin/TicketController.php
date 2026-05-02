<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with('user')
            ->latest()
            ->paginate(30);

        return view('superadmin.tickets.index', compact('tickets'));
    }

    public function show(Ticket $ticket)
    {
        $ticket->load('user');
        return view('superadmin.tickets.show', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'status'      => 'required|in:open,working_on_it,resolved,closed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $ticket->update($data);

        return back()->with('success', "Ticket {$ticket->ticket_number} updated.");
    }
}
