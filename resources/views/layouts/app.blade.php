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

  {{-- Desktop nav links (flex: 1 pushes right side over) --}}
  <div class="nav-links" id="nav-links">
    <a href="{{ route('dashboard') }}"      class="{{ request()->routeIs('dashboard')       ? 'active' : '' }}"><span>Dashboard</span></a>
    <a href="{{ route('clients.index') }}"  class="{{ request()->routeIs('clients.*')       ? 'active' : '' }}"><span>Clients</span></a>
    <a href="{{ route('calendar.index') }}" class="{{ request()->routeIs('calendar.*')      ? 'active' : '' }}"><span>Calendar</span></a>
    <a href="{{ route('notifications.index') }}" class="{{ request()->routeIs('notifications.*') ? 'active' : '' }}"><span>Alerts</span></a>
    @auth
      @if(auth()->user()->isAdmin())
        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}"><span>Admin</span></a>
      @endif
    @endauth
  </div>

  <div class="navbar-right">
    {{-- Hamburger (mobile only) --}}
    <button class="hamburger-btn" id="hamburger-btn" aria-label="Menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
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

    {{-- User menu --}}
    @auth
    <div class="user-menu-wrap">
      <button id="user-menu-btn" class="user-menu-btn" title="Account">
        <span class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
        <span class="user-name">{{ auth()->user()->name }}</span>
        <span style="font-size:.7rem;opacity:.75;">&#9660;</span>
      </button>
      <div id="user-dropdown" class="user-dropdown">
        <div class="user-dropdown-header">
          <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->name }}</div>
          <div style="font-size:.78rem;color:var(--muted);margin-top:.1rem;">{{ auth()->user()->email }}</div>
        </div>
        <a href="{{ route('password.change') }}" class="user-dropdown-item">
          &#128274; Change Password
        </a>
        <div class="user-dropdown-divider"></div>
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
          @csrf
          <button type="submit" class="user-dropdown-item user-dropdown-item--danger" style="width:100%;text-align:left;">
            &#8594; Sign Out
          </button>
        </form>
      </div>
    </div>
    @endauth
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
