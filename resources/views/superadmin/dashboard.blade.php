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

{{-- ── Action required ─────────────────────── --}}
@if($pendingApprovals->isNotEmpty() || $suspendedAdmins->isNotEmpty())
<div class="grid-2col" style="margin-bottom:1.25rem;">

  {{-- Pending approvals --}}
  <div class="card" style="border-left:4px solid var(--warning);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;color:var(--warning);">
        &#9888; Pending Approvals
        <span style="font-size:.75rem;font-weight:400;margin-left:.4rem;color:var(--muted);">({{ $pendingApprovals->count() }})</span>
      </div>
      <a href="{{ route('superadmin.payments.index', ['status'=>'pending']) }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($pendingApprovals as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $admin->name }}</div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->email }}
          @if($admin->phone) &bull; <a href="tel:{{ $admin->phone }}" style="color:inherit;">{{ $admin->phone }}</a> @endif
        </div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->business_type ?? '—' }}
          &bull; {{ $admin->planLabel() }}
          @if($admin->payment_submitted_at) &bull; {{ $admin->payment_submitted_at->diffForHumans() }} @endif
        </div>
      </div>
      <a href="{{ route('superadmin.payments.show', $admin) }}"
         class="btn btn-sm btn-warning" style="white-space:nowrap;margin-left:.75rem;">Review</a>
    </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No pending approvals.</p>
    @endforelse
  </div>

  {{-- Suspended admins --}}
  <div class="card" style="border-left:4px solid var(--danger);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;color:var(--danger);">
        &#128274; Suspended Admins
        <span style="font-size:.75rem;font-weight:400;margin-left:.4rem;color:var(--muted);">({{ $suspendedAdmins->count() }})</span>
      </div>
      <a href="{{ route('superadmin.admins.index', ['status'=>'suspended']) }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($suspendedAdmins as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $admin->name }}</div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->email }}
          @if($admin->phone) &bull; <a href="tel:{{ $admin->phone }}" style="color:inherit;">{{ $admin->phone }}</a> @endif
        </div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->planLabel() }}
          @if($admin->plan_expires_at) &bull; expired {{ $admin->plan_expires_at->format('d M Y') }} @endif
        </div>
      </div>
      <div class="d-flex gap-2" style="margin-left:.75rem;">
        <form method="POST" action="{{ route('superadmin.admins.unsuspend', $admin) }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-success" style="white-space:nowrap;">Reactivate</button>
        </form>
        <a href="{{ route('superadmin.admins.show', $admin) }}" class="btn btn-sm btn-secondary">View</a>
      </div>
    </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No suspended admins.</p>
    @endforelse
  </div>

</div>
@endif

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

{{-- ── Tools ───────────────────────────────── --}}
<div class="card" style="margin-bottom:1.25rem;">
  <div class="card-title">System Tools</div>
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
      <div style="font-size:.9rem;font-weight:600;">Clear Cache</div>
      <div class="text-muted" style="font-size:.8rem;">Clears config, route, view, and application cache. Use after deploying updates.</div>
    </div>
    <form method="POST" action="{{ route('superadmin.cache.clear') }}">
      @csrf
      <button type="submit" class="btn btn-secondary">&#128465; Clear Cache</button>
    </form>
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
