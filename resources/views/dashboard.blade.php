@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <button id="btn-update-alerts" class="btn btn-warning">🔔 Update Alerts</button>
</div>

<div id="alert-result" class="alert" style="display:none"></div>

@if(session('trial_started'))
  <div class="alert alert-success" style="margin-bottom:1rem;">
    <strong>&#127775; Welcome! Your 14-day free trial has started.</strong>
    Explore all features with no payment needed. Upgrade anytime before the trial ends.
  </div>
@endif

{{-- ── Plan / trial alerts ─────────────────── --}}
@if($planAlert === 'trial_active')
  <div class="alert" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;margin-bottom:1rem;">
    <strong>&#127775; Free Trial Active:</strong>
    {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }} remaining in your free trial.
    <a href="#subscription-info" style="color:#2563eb;font-weight:600;margin-left:.5rem;">Upgrade to a paid plan &rarr;</a>
  </div>
@elseif($planAlert === 'trial_ending')
  <div class="alert alert-warning" style="margin-bottom:1rem;">
    <strong>&#9888; Trial Ending Soon:</strong>
    Only {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }} left in your free trial.
    <a href="#subscription-info" style="color:inherit;font-weight:600;text-decoration:underline;margin-left:.3rem;">Upgrade now</a> to keep your data and access.
  </div>
@elseif($planAlert === 'expiring')
  <div class="alert alert-warning" style="margin-bottom:1rem;">
    <strong>&#9888; Subscription Expiring Soon:</strong>
    Your {{ auth()->user()->planLabel() }} plan expires in {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }}.
    Please contact support to renew before it expires.
  </div>
@elseif($planAlert === 'grace')
  @php $overdue = abs($daysLeft); @endphp
  <div class="alert alert-danger" style="margin-bottom:1rem;">
    <strong>&#128680; Subscription Expired:</strong>
    Your {{ auth()->user()->planLabel() }} plan expired {{ $overdue }} day{{ $overdue == 1 ? '' : 's' }} ago.
    You are in a grace period. Please renew immediately to avoid account suspension.
  </div>
@endif

{{-- ── Stats ────────────────────────────────── --}}
<div class="stats-grid">
  <a href="{{ route('clients.index') }}" class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </a>
  <a href="{{ route('notifications.index') }}" class="stat-card">
    <div class="num" id="stat-unread">{{ $stats['unread_notifications'] }}</div>
    <div class="label">Unread Alerts</div>
  </a>
  <a href="{{ route('clients.index', ['visit' => 'week']) }}" class="stat-card">
    <div class="num">{{ $stats['upcoming_visits'] }}</div>
    <div class="label">Visits (next 7 days)</div>
  </a>
  <a href="{{ route('clients.index', ['visit' => 'overdue']) }}" class="stat-card" style="{{ $stats['not_contacted'] > 0 ? 'border-top:3px solid var(--warning);' : '' }}">
    <div class="num" style="{{ $stats['not_contacted'] > 0 ? 'color:var(--warning);' : '' }}">
      {{ $stats['not_contacted'] }}
    </div>
    <div class="label">Not Contacted (30d)</div>
  </a>
  <a href="{{ route('calendar.index') }}" class="stat-card" style="{{ $stats['upcoming_events'] > 0 ? 'border-top:3px solid var(--primary);' : '' }}">
    <div class="num" style="{{ $stats['upcoming_events'] > 0 ? 'color:var(--primary);' : '' }}">
      {{ $stats['upcoming_events'] }}
    </div>
    <div class="label">Birthdays/Anniv. (7d)</div>
  </a>
  <div class="stat-card" style="{{ $stats['interactions_week'] > 0 ? 'border-top:3px solid var(--success);' : '' }}">
    <div class="num" style="{{ $stats['interactions_week'] > 0 ? 'color:var(--success);' : '' }}">
      {{ $stats['interactions_week'] }}
    </div>
    <div class="label">Interactions (7d)</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['interactions_month'] }}</div>
    <div class="label">Interactions (30d)</div>
  </div>
</div>

{{-- ── Upcoming birthdays & anniversaries ───── --}}
@if($upcomingEvents->isNotEmpty())
<div class="card" style="margin-bottom:1rem;border-left:4px solid var(--primary);">
  <div class="card-title">&#127881; Upcoming This Week</div>
  <div style="display:grid;gap:.4rem;">
    @foreach($upcomingEvents as $event)
    @php $next = $event->nextOccurrence(); $daysAway = (int) now()->startOfDay()->diffInDays($next); @endphp
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.45rem 0;border-bottom:1px solid var(--border);">
      <div>
        <a href="{{ route('clients.show', $event->client) }}"
           style="font-weight:600;font-size:.9rem;text-decoration:none;color:var(--text);">
          {{ $event->client->name }}
        </a>
        <span class="badge {{ $event->badgeClass() }}" style="margin-left:.4rem;font-size:.72rem;">
          {{ $event->typeLabel() }}
        </span>
        @if($event->label)
          <span class="text-muted" style="font-size:.78rem;margin-left:.3rem;">{{ $event->label }}</span>
        @endif
      </div>
      <div style="text-align:right;flex-shrink:0;margin-left:.75rem;">
        <div style="font-size:.82rem;font-weight:600;color:{{ $daysAway === 0 ? 'var(--danger)' : 'var(--primary)' }};">
          {{ $daysAway === 0 ? 'Today' : "in {$daysAway}d" }}
        </div>
        <div class="text-muted" style="font-size:.72rem;">{{ $next->format('d M') }}</div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ── Subscription info (admins only) ─────── --}}
@if(auth()->user()->isAdmin())
@php $u = auth()->user(); @endphp
<div class="card" id="subscription-info" style="margin-bottom:1rem;">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem;">
    <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
      <div>
        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Plan</div>
        <div style="font-weight:700;font-size:.95rem;">{{ $u->planLabel() }}</div>
      </div>
      <div>
        <div class="text-muted" style="font-size:.75rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">
          {{ $u->isOnTrial() ? 'Trial Ends' : 'Expiry' }}
        </div>
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
        <div style="font-weight:600;font-size:.95rem;color:{{ $daysLeft < 0 ? 'var(--danger)' : ($daysLeft <= 7 ? 'var(--warning)' : 'var(--success)') }};">
          @if($daysLeft < 0)
            Expired {{ abs($daysLeft) }}d ago
          @else
            {{ $daysLeft }} day{{ $daysLeft == 1 ? '' : 's' }}
          @endif
        </div>
      </div>
      @endif
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
      @if($u->is_suspended)
        <span class="badge badge-danger" style="padding:.3rem .75rem;font-size:.78rem;">&#128274; Suspended</span>
      @elseif($u->isOnTrial())
        <span class="badge" style="background:#dbeafe;color:#1e40af;padding:.3rem .75rem;font-size:.78rem;">
          &#127775; Free Trial
        </span>
        <a href="{{ route('account.trial_expired') }}"
           style="font-size:.8rem;color:#2563eb;font-weight:600;text-decoration:none;white-space:nowrap;">
          Upgrade &rarr;
        </a>
      @elseif($u->plan_type)
        <span class="badge {{ $planAlert === 'grace' ? 'badge-danger' : ($planAlert === 'expiring' ? 'badge-warning' : 'badge-success') }}"
              style="padding:.3rem .75rem;font-size:.78rem;">
          {{ $planAlert === 'grace' ? 'Grace Period' : ($planAlert === 'expiring' ? 'Expiring Soon' : 'Active') }}
        </span>
      @endif
    </div>
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
  <a href="{{ route('notifications.index') }}" class="btn btn-primary" style="background:var(--success);border-color:var(--success);">&#128276; Alerts @if($stats['unread_notifications'] > 0)<span style="background:rgba(255,255,255,.25);border-radius:8px;padding:0 .4rem;margin-left:.3rem;font-size:.8rem;">{{ $stats['unread_notifications'] }}</span>@endif</a>
  <a href="{{ route('calendar.index') }}"  class="btn btn-secondary">&#128197; Calendar</a>
  <a href="{{ route('clients.index') }}"   class="btn btn-secondary">&#128101; All Clients</a>
  @if($stats['not_contacted'] > 0)
  <a href="{{ route('clients.index', ['visit' => 'overdue']) }}" class="btn btn-secondary" style="color:var(--warning);">
    &#9888; {{ $stats['not_contacted'] }} Not Contacted
  </a>
  @endif
</div>
@endsection
