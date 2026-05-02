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
