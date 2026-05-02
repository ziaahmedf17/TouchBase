<div class="admin-sub-nav">
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
  <a href="{{ route('superadmin.payments.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.payments.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#128184; Payments
  </a>
  <a href="{{ route('superadmin.payment-accounts.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.payment-accounts.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#127974; Bank Accounts
  </a>
  <a href="{{ route('superadmin.plans.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.plans.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#128176; Plans
  </a>
</div>
