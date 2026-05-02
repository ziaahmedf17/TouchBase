@extends('layouts.app')
@section('title', $ticket->ticket_number)

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $ticket->ticket_number }}</h1>
    <div class="text-muted" style="font-size:.85rem;">
      From <strong>{{ $ticket->user->name }}</strong> ({{ $ticket->user->email }})
      &bull; {{ $ticket->created_at->format('d M Y') }} at {{ $ticket->created_at->format('H:i') }}
    </div>
  </div>
  <a href="{{ route('superadmin.tickets.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="grid-sidebar">

  {{-- Left: ticket content --}}
  <div style="display:grid;gap:1rem;">

    <div style="padding:.75rem 1.25rem;border-radius:var(--radius);font-weight:600;font-size:.9rem;{{ $ticket->statusBadgeStyle() }}">
      Status: {{ $ticket->statusLabel() }}
    </div>

    <div class="card">
      <div class="card-title">Subject</div>
      <p style="margin:0;font-size:1rem;font-weight:600;">{{ $ticket->subject }}</p>
    </div>

    <div class="card">
      <div class="card-title">Description</div>
      <p style="margin:0;white-space:pre-line;font-size:.9rem;line-height:1.7;">{{ $ticket->description }}</p>
    </div>

    @if($ticket->admin_notes)
    <div class="card" style="border-left:4px solid var(--primary);">
      <div class="card-title">Current Notes</div>
      <p style="margin:0;font-size:.9rem;line-height:1.7;">{{ $ticket->admin_notes }}</p>
    </div>
    @endif

  </div>

  {{-- Right: update panel --}}
  <div class="card sticky-panel">
    <div class="card-title">Update Ticket</div>
    <form method="POST" action="{{ route('superadmin.tickets.update', $ticket) }}">
      @csrf @method('PUT')

      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="open"          {{ $ticket->status === 'open'          ? 'selected' : '' }}>Open</option>
          <option value="working_on_it" {{ $ticket->status === 'working_on_it' ? 'selected' : '' }}>Working on it</option>
          <option value="resolved"      {{ $ticket->status === 'resolved'      ? 'selected' : '' }}>Resolved</option>
          <option value="closed"        {{ $ticket->status === 'closed'        ? 'selected' : '' }}>Closed</option>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Notes for Admin</label>
        <textarea name="admin_notes" rows="4" class="form-control"
                  placeholder="Add a note visible to the admin..."
                  maxlength="2000">{{ old('admin_notes', $ticket->admin_notes) }}</textarea>
      </div>

      <button type="submit" class="btn btn-primary" style="width:100%;">Save Update</button>
    </form>
  </div>

</div>
@endsection
