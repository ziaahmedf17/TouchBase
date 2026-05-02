@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <button id="btn-update-alerts" class="btn btn-warning">🔔 Update Alerts</button>
</div>

<div id="alert-result" class="alert" style="display:none"></div>

{{-- ── Plan expiry alerts ───────────────────── --}}
@if($planAlert === 'expiring')
  <div class="alert" style="background:#fef3c7;color:#92400e;border:1px solid #fcd34d;margin-bottom:1rem;">
    <strong>&#9888; Subscription Expiring Soon</strong> —
    Your {{ auth()->user()->planLabel() }} plan expires in {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }}.
    Please contact support to renew before it expires.
  </div>
@elseif($planAlert === 'grace')
  @php $overdue = abs($daysLeft); @endphp
  <div class="alert" style="background:#fee2e2;color:#991b1b;border:1px solid #fca5a5;margin-bottom:1rem;">
    <strong>&#128680; Subscription Expired</strong> —
    Your {{ auth()->user()->planLabel() }} plan expired {{ $overdue }} day{{ $overdue == 1 ? '' : 's' }} ago.
    You are in a grace period. Please renew immediately to avoid account suspension.
  </div>
@endif

{{-- ── Stats ────────────────────────────────── --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </div>
  <div class="stat-card">
    <div class="num" id="stat-unread">{{ $stats['unread_notifications'] }}</div>
    <div class="label">Unread Alerts</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['upcoming_visits'] }}</div>
    <div class="label">Visits (next 7 days)</div>
  </div>
</div>

{{-- ── Subscription info (admins only) ─────── --}}
@if(auth()->user()->isAdmin())
@php $u = auth()->user(); @endphp
<div class="card" style="margin-bottom:1rem;">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
      <div>
        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Plan</div>
        <div style="font-weight:700;font-size:.95rem;">{{ $u->planLabel() }}</div>
      </div>
      <div>
        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Expiry</div>
        <div style="font-weight:600;font-size:.95rem;">
          @if($u->plan_type === 'lifetime')
            <span style="color:var(--success);">Never</span>
          @elseif($u->plan_expires_at)
            {{ $u->plan_expires_at->format('d M Y') }}
          @else
            <span class="text-muted">—</span>
          @endif
        </div>
      </div>
      @if($daysLeft !== null && $u->plan_type !== 'lifetime')
      <div>
        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Remaining</div>
        <div style="font-weight:600;font-size:.95rem;
          color:{{ $daysLeft < 0 ? '#991b1b' : ($daysLeft <= 14 ? '#92400e' : 'var(--success)') }};">
          @if($daysLeft < 0)
            Expired {{ abs($daysLeft) }}d ago
          @else
            {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }}
          @endif
        </div>
      </div>
      @endif
    </div>
    @if($u->is_suspended)
      <span style="padding:.3rem .75rem;border-radius:12px;font-size:.78rem;font-weight:700;background:#fee2e2;color:#991b1b;">
        &#128274; Suspended
      </span>
    @elseif($u->plan_type)
      <span style="padding:.3rem .75rem;border-radius:12px;font-size:.78rem;font-weight:700;
        {{ $planAlert === 'grace' ? 'background:#fee2e2;color:#991b1b;' : ($planAlert === 'expiring' ? 'background:#fef3c7;color:#92400e;' : 'background:#dcfce7;color:#166534;') }}">
        {{ $planAlert === 'grace' ? 'Grace Period' : ($planAlert === 'expiring' ? 'Expiring Soon' : 'Active') }}
      </span>
    @endif
  </div>
</div>
@endif

{{-- ── Recent Notifications ─────────────────── --}}
<div class="card">
  <div class="card-title">Recent Alerts</div>
  <div id="recent-alerts-body">
    @include('partials.recent_notifications')
  </div>
</div>

{{-- ── Quick actions ────────────────────────── --}}
<div class="d-flex gap-2 mt-3" style="flex-wrap:wrap;">
  @can('clients.create')<a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add Client</a>@endcan
  <a href="{{ route('calendar.index') }}"  class="btn btn-secondary">&#128197; Calendar</a>
  <a href="{{ route('clients.index') }}"   class="btn btn-secondary">&#128101; All Clients</a>
</div>
@endsection
