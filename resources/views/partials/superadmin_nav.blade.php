<div style="display:flex;gap:.35rem;margin-bottom:1.25rem;border-bottom:2px solid var(--border);padding-bottom:.75rem;">
  <a href="{{ route('superadmin.dashboard') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.dashboard') ? 'btn-primary' : 'btn-secondary' }}">
    &#9711; Dashboard
  </a>
  <a href="{{ route('superadmin.admins.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.admins.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#127968; Admins
  </a>
  <a href="{{ route('superadmin.tickets.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.tickets.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#127915; Tickets
  </a>
</div>
