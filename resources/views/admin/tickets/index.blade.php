@extends('layouts.app')
@section('title', 'Support Tickets')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Support Tickets</h1>
  <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">+ Raise Ticket</a>
</div>

@if(session('ticket_submitted'))
<div class="card" style="border-left:4px solid var(--success);background:#f0fdf4;padding:1.25rem 1.5rem;margin-bottom:1.25rem;">
  <div style="font-size:1.1rem;font-weight:700;color:var(--success);margin-bottom:.25rem;">
    &#10003; Ticket #{{ session('ticket_submitted') }} submitted successfully
  </div>
  <div style="color:#166534;font-size:.9rem;">Team will reach out to you soon.</div>
  <div class="text-muted" style="font-size:.8rem;margin-top:.35rem;">
    Submitted on {{ now()->format('d M Y \a\t H:i') }}
  </div>
</div>
@endif

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($tickets->isEmpty())
  <div class="empty-state">
    <div class="icon">&#127915;</div>
    <p>No tickets yet. <a href="{{ route('admin.tickets.create') }}">Raise your first ticket</a>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Ticket #</th>
          <th>Subject</th>
          <th>Status</th>
          <th>Notes from Team</th>
          <th>Submitted</th>
        </tr>
      </thead>
      <tbody>
        @foreach($tickets as $ticket)
        <tr style="cursor:pointer;" onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
          <td data-label="Ticket #">
            <code style="font-size:.8rem;background:var(--surface);padding:.1rem .35rem;border-radius:4px;border:1px solid var(--border);">
              {{ $ticket->ticket_number }}
            </code>
          </td>
          <td data-label="Subject">
            <div style="font-weight:600;">{{ $ticket->subject }}</div>
            <div class="text-muted" style="font-size:.8rem;margin-top:.15rem;">
              {{ Str::limit($ticket->description, 80) }}
            </div>
          </td>
          <td data-label="Status">
            <span class="badge {{ $ticket->statusBadgeClass() }}">
              {{ $ticket->statusLabel() }}
            </span>
          </td>
          <td data-label="Notes from Team">
            @if($ticket->admin_notes)
              <span style="font-size:.85rem;">{{ Str::limit($ticket->admin_notes, 60) }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Submitted" style="white-space:nowrap;">
            <div>{{ $ticket->created_at->format('d M Y') }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $ticket->created_at->format('H:i') }}</div>
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
