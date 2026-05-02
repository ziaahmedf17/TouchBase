@extends('layouts.app')
@section('title', 'Platform Overview')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">Platform Overview</h1>
  <span class="text-muted" style="font-size:.85rem;">{{ now()->format('d M Y') }}</span>
</div>

{{-- ── Main stats ───────────────────────────── --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:1.25rem;">
  <div class="stat-card">
    <div class="num">{{ $stats['total_admins'] }}</div>
    <div class="label">Total Admins</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--success);">{{ $stats['active_admins'] }}</div>
    <div class="label">Active</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--warning);">{{ $stats['pending_admins'] }}</div>
    <div class="label">Pending Approval</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--danger);">{{ $stats['suspended_admins'] }}</div>
    <div class="label">Suspended</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </div>
  <div class="stat-card">
    <div class="num" style="color:var(--danger);">{{ $stats['open_tickets'] }}</div>
    <div class="label">Open Tickets</div>
  </div>
</div>

{{-- ── Plan distribution + upcoming renewals ── --}}
<div class="grid-2col" style="margin-bottom:1.25rem;">

  {{-- Plan breakdown --}}
  <div class="card">
    <div class="card-title">Active Subscriptions</div>
    <div style="display:grid;gap:.75rem;">
      @foreach(['monthly','yearly','lifetime'] as $slug)
        @php
          $p     = $plans[$slug] ?? null;
          $count = $planCounts[$slug] ?? 0;
        @endphp
        @if($p)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .75rem;background:var(--bg);border-radius:var(--radius);">
          <div>
            <div style="font-weight:600;font-size:.9rem;">{{ $p->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $p->formattedPrice() }}
              @if($slug !== 'lifetime') / {{ $slug === 'monthly' ? 'mo' : 'yr' }} @endif
            </div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:1.4rem;font-weight:700;line-height:1;">{{ $count }}</div>
            <div class="text-muted" style="font-size:.72rem;">admin{{ $count == 1 ? '' : 's' }}</div>
          </div>
        </div>
        @endif
      @endforeach
    </div>
  </div>

  {{-- Upcoming renewals --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Renewals Due (30 days)</div>
      <span style="font-size:.8rem;padding:.15rem .5rem;border-radius:10px;background:var(--bg);color:var(--muted);">
        {{ $upcomingRenewals->count() }}
      </span>
    </div>
    @forelse($upcomingRenewals as $admin)
      @php $days = (int) now()->diffInDays($admin->plan_expires_at, false); @endphp
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.45rem 0;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:.88rem;font-weight:600;">
            <a href="{{ route('superadmin.admins.show', $admin) }}" style="text-decoration:none;color:inherit;">{{ $admin->name }}</a>
          </div>
          <div class="text-muted" style="font-size:.75rem;">{{ ucfirst($admin->plan_type) }} &bull; expires {{ $admin->plan_expires_at->format('d M Y') }}</div>
        </div>
        <span style="font-size:.75rem;padding:.15rem .5rem;border-radius:10px;white-space:nowrap;
          {{ $days <= 7 ? 'background:#fee2e2;color:#991b1b;' : 'background:#fef3c7;color:#92400e;' }}">
          {{ $days }}d
        </span>
      </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No renewals due in the next 30 days.</p>
    @endforelse
  </div>

</div>

{{-- ── Recent admins + pending tickets ────── --}}
<div class="grid-2col">

  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Recent Admins</div>
      <a href="{{ route('superadmin.admins.index') }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($recentAdmins as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">
          <a href="{{ route('superadmin.admins.show', $admin) }}" style="text-decoration:none;color:inherit;">{{ $admin->name }}</a>
        </div>
        <div class="text-muted" style="font-size:.78rem;">{{ $admin->email }}</div>
      </div>
      <div style="text-align:right;">
        <span class="badge badge-custom" style="font-size:.68rem;{{ $admin->accountStatusBadgeStyle() }}">
          {{ $admin->accountStatusLabel() }}
        </span>
        <div class="text-muted" style="font-size:.72rem;margin-top:.2rem;">{{ $admin->created_at->format('d M Y') }}</div>
      </div>
    </div>
    @empty
      <p class="text-muted">No admins yet.</p>
    @endforelse
  </div>

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
          {{ $ticket->user->name }} &bull; {{ Str::limit($ticket->subject, 38) }}
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
