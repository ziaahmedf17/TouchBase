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
  <a href="{{ route('superadmin.activity.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.activity.*') ? 'btn-primary' : 'btn-secondary' }}">
    &#128221; Activity
  </a>
  <a href="{{ route('superadmin.contacts.index') }}"
     class="btn btn-sm {{ request()->routeIs('superadmin.contacts.*') ? 'btn-primary' : 'btn-secondary' }}"
     style="position:relative;">
    &#9993; Inquiries
    @php $unread = \App\Models\ContactMessage::where('is_read', false)->count(); @endphp
    @if($unread > 0)
      <span style="position:absolute;top:-5px;right:-5px;background:var(--danger);color:#fff;
                   font-size:.6rem;font-weight:700;border-radius:50%;width:16px;height:16px;
                   display:flex;align-items:center;justify-content:center;line-height:1;">
        {{ $unread > 9 ? '9+' : $unread }}
      </span>
    @endif
  </a>
</div>
