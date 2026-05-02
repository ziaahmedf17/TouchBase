@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Support Tickets</h1>
  <span class="text-muted" style="font-size:.9rem;">{{ $tickets->total() }} total</span>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($tickets->isEmpty())
  <div class="empty-state">
    <div class="icon">&#127915;</div>
    <p>No tickets submitted yet.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Ticket #</th>
          <th>Admin</th>
          <th>Subject</th>
          <th>Status</th>
          <th>Submitted</th>
          <th style="min-width:260px;">Update</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $ticket)
        <tr>
          <td data-label="Ticket #">
            <code style="font-size:.8rem;background:var(--surface);padding:.1rem .35rem;border-radius:4px;border:1px solid var(--border);">
              {{ $ticket->ticket_number }}
            </code>
          </td>
          <td data-label="Admin">
            <div style="font-weight:600;">{{ $ticket->user->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $ticket->user->email }}</div>
          </td>
          <td data-label="Subject">
            <a href="{{ route('superadmin.tickets.show', $ticket) }}" style="font-weight:600;text-decoration:none;color:var(--text);">
              {{ $ticket->subject }}
            </a>
            <div class="text-muted" style="font-size:.8rem;margin-top:.15rem;">
              {{ Str::limit($ticket->description, 80) }}
            </div>
            @if($ticket->admin_notes)
              <div style="font-size:.8rem;color:var(--muted);margin-top:.25rem;border-top:1px solid var(--border);padding-top:.25rem;">
                <strong>Note:</strong> {{ Str::limit($ticket->admin_notes, 60) }}
              </div>
            @endif
          </td>
          <td data-label="Status">
            <span class="badge badge-custom" style="{{ $ticket->statusBadgeStyle() }}">
              {{ $ticket->statusLabel() }}
            </span>
          </td>
          <td data-label="Submitted" style="white-space:nowrap;">
            <div>{{ $ticket->created_at->format('d M Y') }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $ticket->created_at->format('H:i') }}</div>
          </td>
          <td data-label="Update">
            <form method="POST" action="{{ route('superadmin.tickets.update', $ticket) }}"
                  style="display:flex;flex-direction:column;gap:.4rem;">
              @csrf @method('PUT')
              <select name="status" class="form-control" style="font-size:.85rem;padding:.3rem .5rem;">
                <option value="open"          {{ $ticket->status === 'open'          ? 'selected' : '' }}>Open</option>
                <option value="working_on_it" {{ $ticket->status === 'working_on_it' ? 'selected' : '' }}>Working on it</option>
                <option value="resolved"      {{ $ticket->status === 'resolved'      ? 'selected' : '' }}>Resolved</option>
                <option value="closed"        {{ $ticket->status === 'closed'        ? 'selected' : '' }}>Closed</option>
              </select>
              <input type="text" name="admin_notes"
                     class="form-control" style="font-size:.85rem;padding:.3rem .5rem;"
                     placeholder="Add a note (optional)"
                     value="{{ $ticket->admin_notes ?? '' }}">
              <button type="submit" class="btn btn-sm btn-primary">Update</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{ $tickets->links('partials.pagination') }}
@endif
@endsection
