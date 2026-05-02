@extends('layouts.app')
@section('title', 'Platform Overview')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">Platform Overview</h1>
  <span class="text-muted" style="font-size:.85rem;">{{ now()->format('d M Y') }}</span>
</div>

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(160px,1fr));margin-bottom:1.5rem;">
  <div class="stat-card">
    <div class="num">{{ $stats['total_admins'] }}</div>
    <div class="label">Admins</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['total_sub_users'] }}</div>
    <div class="label">Sub-Users</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--danger);">{{ $stats['open_tickets'] }}</div>
    <div class="label">Open Tickets</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--warning);">{{ $stats['working_tickets'] }}</div>
    <div class="label">In Progress</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['total_tickets'] }}</div>
    <div class="label">Total Tickets</div>
  </div>
</div>

<div class="grid-2col">

  {{-- Recently Registered Admins --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Recent Admins</div>
      <a href="{{ route('superadmin.admins.index') }}" style="font-size:.82rem;">View all</a>
    </div>

    @forelse($recentAdmins as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $admin->name }}</div>
        <div class="text-muted" style="font-size:.78rem;">{{ $admin->email }}</div>
      </div>
      <div class="text-muted" style="font-size:.78rem;white-space:nowrap;margin-left:.75rem;">
        {{ $admin->created_at->format('d M Y') }}
      </div>
    </div>
    @empty
      <p class="text-muted">No admins yet.</p>
    @endforelse
  </div>

  {{-- Open / In-Progress Tickets --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Pending Tickets</div>
      <a href="{{ route('superadmin.tickets.index') }}" style="font-size:.82rem;">View all</a>
    </div>

    @forelse($recentTickets as $ticket)
    <a href="{{ route('superadmin.tickets.show', $ticket) }}"
       style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);text-decoration:none;color:inherit;">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $ticket->ticket_number }}</div>
        <div class="text-muted" style="font-size:.78rem;">
          {{ $ticket->user->name }} &bull; {{ Str::limit($ticket->subject, 40) }}
        </div>
      </div>
      <span class="badge badge-custom" style="white-space:nowrap;margin-left:.75rem;{{ $ticket->statusBadgeStyle() }}">
        {{ $ticket->statusLabel() }}
      </span>
    </a>
    @empty
      <p class="text-muted">No pending tickets.</p>
    @endforelse
  </div>

</div>
@endsection
