<div style="display:flex;gap:.35rem;margin-bottom:1.25rem;border-bottom:2px solid var(--border);padding-bottom:.75rem;">
  <a href="{{ route('admin.roles.index') }}"
     class="btn btn-sm {{ request()->routeIs('admin.roles.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#128100; Roles
  </a>
  <a href="{{ route('admin.permissions.index') }}"
     class="btn btn-sm {{ request()->routeIs('admin.permissions.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#128274; Permissions
  </a>
</div>
