@extends('layouts.app')
@section('title', $ticket->ticket_number)

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $ticket->ticket_number }}</h1>
    <div class="text-muted" style="font-size:.85rem;">
      Submitted on {{ $ticket->created_at->format('d M Y') }} at {{ $ticket->created_at->format('H:i') }}
    </div>
  </div>
  <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div style="display:grid;gap:1rem;max-width:720px;">

  {{-- Status banner --}}
  <div style="padding:.75rem 1.25rem;border-radius:var(--radius);font-weight:600;font-size:.9rem;{{ $ticket->statusBadgeStyle() }}">
    Status: {{ $ticket->statusLabel() }}
  </div>

  {{-- Ticket details --}}
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
    <div class="card-title">Notes from Team</div>
    <p style="margin:0;font-size:.9rem;line-height:1.7;">{{ $ticket->admin_notes }}</p>
  </div>
  @endif

  @if(in_array($ticket->status, ['open', 'working_on_it']))
  <p class="text-muted" style="font-size:.85rem;margin:0;">
    &#128338; Team will reach out to you soon. You'll see updates reflected here.
  </p>
  @endif

</div>
@endsection
