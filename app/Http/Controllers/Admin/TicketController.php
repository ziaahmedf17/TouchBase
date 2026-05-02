<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('admin.tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('admin.tickets.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'description' => 'required|string|max:5000',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => 'TKT-' . str_pad(
                Ticket::max('id') + 1, 5, '0', STR_PAD_LEFT
            ),
            'user_id'       => Auth::id(),
            'subject'       => $data['subject'],
            'description'   => $data['description'],
            'status'        => 'open',
        ]);

        return redirect()->route('admin.tickets.index')
            ->with('ticket_submitted', $ticket->ticket_number);
    }
}
