<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Ticket::with('user');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        if ($status && in_array($status, ['open', 'working_on_it', 'resolved', 'closed'])) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()->paginate(30)->withQueryString();

        return view('superadmin.tickets.index', compact('tickets', 'search', 'status'));
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
