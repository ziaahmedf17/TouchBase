@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <button id="btn-update-alerts" class="btn btn-warning">&#128276; Update Alerts</button>
</div>

<div id="alert-result" class="alert"></div>

{{-- ── Stats ────────────────────────────────── --}}
<div class="stats-grid">
  <div class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $stats['unread_notifications'] }}</div>
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

  @forelse($recentNotifications as $n)
  <div class="notif-item {{ $n->is_read ? '' : 'unread' }}" style="border-radius:6px;margin-bottom:.5rem;" data-mark-read="{{ $n->id }}">
    <div class="d-flex" style="justify-content:space-between;align-items:flex-start;">
      <div>
        <div class="notif-item-title">{{ $n->message }}</div>
        <div class="notif-item-meta">
          <span class="badge {{ $n->event?->badgeClass() }}">{{ $n->event?->typeLabel() }}</span>
          &bull; {{ $n->triggered_date->format('d M Y') }}
        </div>
      </div>
      <button data-delete-notif="{{ $n->id }}" class="btn btn-icon btn-secondary" title="Dismiss">&times;</button>
    </div>
    @if($n->client && $n->client->phone)
    <div class="notif-actions mt-2">
      <a href="{{ $n->client->telUrl() }}"       class="btn btn-sm btn-success">&#128222; Call</a>
      <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">&#128172; WhatsApp</a>
      <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
    </div>
    @endif
  </div>
  @empty
  <div class="empty-state">
    <div class="icon">&#128276;</div>
    <p>No alerts yet. Click <strong>Update Alerts</strong> to check.</p>
  </div>
  @endforelse

  @if($recentNotifications->count() > 0)
  <div style="text-align:right;margin-top:.75rem;">
    <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm">View all alerts &rarr;</a>
  </div>
  @endif
</div>

{{-- ── Quick actions ────────────────────────── --}}
<div class="d-flex gap-2 mt-3" style="flex-wrap:wrap;">
  <a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add Client</a>
  <a href="{{ route('calendar.index') }}"  class="btn btn-secondary">&#128197; Calendar</a>
  <a href="{{ route('clients.index') }}"   class="btn btn-secondary">&#128101; All Clients</a>
</div>
@endsection
