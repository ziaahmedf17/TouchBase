<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'TouchBase') — TouchBase CRM</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  @stack('head')
</head>
<body>

{{-- ── Navigation ─────────────────────────── --}}
<nav class="navbar">
  <a href="{{ route('dashboard') }}" class="navbar-brand">&#9679; TouchBase</a>

  <div class="nav-links">
    <a href="{{ route('dashboard') }}"      class="{{ request()->routeIs('dashboard')       ? 'active' : '' }}"><span>Dashboard</span></a>
    <a href="{{ route('clients.index') }}"  class="{{ request()->routeIs('clients.*')       ? 'active' : '' }}"><span>Clients</span></a>
    <a href="{{ route('calendar.index') }}" class="{{ request()->routeIs('calendar.*')      ? 'active' : '' }}"><span>Calendar</span></a>
    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}"><span>Alerts</span></a>
  </div>

  <div class="navbar-right">
    {{-- Bell icon with dropdown --}}
    <div class="bell-wrap">
      <button id="bell-btn" class="bell-btn" title="Notifications">&#128276;</button>
      <span id="bell-badge" class="bell-badge hidden">0</span>

      <div id="notif-dropdown" class="notif-dropdown">
        <div class="notif-header">
          <span>Notifications</span>
          <a href="{{ route('notifications.index') }}" style="font-size:.8rem;font-weight:400;">View all</a>
        </div>
        <div class="notif-list" id="notif-list-preview">
          @php
            $previewNotifs = \App\Models\Notification::with('client','event')
              ->latest()->take(6)->get();
          @endphp

          @forelse($previewNotifs as $n)
            <div class="notif-item {{ $n->is_read ? '' : 'unread' }}" data-mark-read="{{ $n->id }}">
              <div class="notif-item-title">{{ $n->message }}</div>
              <div class="notif-item-meta">
                {{ $n->event?->typeLabel() }}
                &bull; {{ $n->triggered_date->format('d M Y') }}
              </div>
              @if($n->client)
              <div class="notif-actions">
                @if($n->client->phone)
                  <a href="{{ $n->client->telUrl() }}" class="btn btn-sm btn-success">Call</a>
                  <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">WhatsApp</a>
                  <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
                @endif
              </div>
              @endif
            </div>
          @empty
            <div class="notif-empty">No notifications yet.</div>
          @endforelse
        </div>
        <div class="notif-footer">
          <a href="{{ route('notifications.readAll') }}"
             onclick="event.preventDefault(); fetch(this.href,{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>location.reload())">
            Mark all read
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

{{-- ── Flash Messages ───────────────────────── --}}
<div class="container" style="padding-bottom:0; margin-bottom:0;">
  @if(session('success'))
    <div class="alert alert-success" data-auto-dismiss>&#10003; {{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger" data-auto-dismiss>&#9888; {{ session('error') }}</div>
  @endif
</div>

{{-- ── Page Content ─────────────────────────── --}}
<main class="container">
  @yield('content')
</main>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
