@extends('layouts.app')
@section('title', 'Admins')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Admins</h1>
  <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary">+ New Admin</a>
</div>

@if(session('success'))
  <div class="alert alert-success" data-auto-dismiss>{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" data-auto-dismiss>{{ session('error') }}</div>
@endif

{{-- Search --}}
<form method="GET" action="{{ route('superadmin.admins.index') }}" class="search-bar">
  <input class="form-control" type="text" name="search"
         placeholder="Search by name, email or phone…"
         value="{{ $search ?? '' }}">
  @if($status ?? null)<input type="hidden" name="status" value="{{ $status }}">@endif
  @if($plan ?? null)<input type="hidden" name="plan" value="{{ $plan }}">@endif
  <button class="btn btn-secondary" type="submit">Search</button>
  @if(($search ?? '') || ($status ?? '') || ($plan ?? ''))
    <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">Clear</a>
  @endif
</form>

{{-- Status filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:.5rem;">
  @php $base = array_filter(['search' => $search ?? null, 'plan' => $plan ?? null]); @endphp
  <a href="{{ route('superadmin.admins.index', $base) }}"
     class="btn btn-sm {{ !($status ?? null) ? 'btn-primary' : 'btn-secondary' }}">All</a>
  <a href="{{ route('superadmin.admins.index', array_merge($base, ['status'=>'active'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'active' ? 'btn-primary' : 'btn-secondary' }}">Active</a>
  <a href="{{ route('superadmin.admins.index', array_merge($base, ['status'=>'pending'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'pending' ? 'btn-warning' : 'btn-secondary' }}">Pending</a>
  <a href="{{ route('superadmin.admins.index', array_merge($base, ['status'=>'suspended'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'suspended' ? 'btn-danger' : 'btn-secondary' }}">Suspended</a>
</div>

{{-- Plan filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:1rem;">
  @php $base2 = array_filter(['search' => $search ?? null, 'status' => $status ?? null]); @endphp
  <a href="{{ route('superadmin.admins.index', $base2) }}"
     class="btn btn-sm {{ !($plan ?? null) ? 'btn-primary' : 'btn-secondary' }}">All Plans</a>
  @foreach(['monthly' => 'Monthly', 'yearly' => 'Yearly', 'lifetime' => 'Lifetime', 'none' => 'No Plan'] as $val => $label)
  <a href="{{ route('superadmin.admins.index', array_merge($base2, ['plan'=>$val])) }}"
     class="btn btn-sm {{ ($plan ?? null) === $val ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
  @endforeach
</div>

@if($admins->isEmpty())
  <div class="empty-state">
    <div class="icon">&#127968;</div>
    <p>No admins yet. <a href="{{ route('superadmin.admins.create') }}">Create the first admin</a>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Sub-Users</th>
          <th>Clients</th>
          <th>Plan / Expiry</th>
          <th>Joined</th>
          <th style="width:160px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($admins as $admin)
        <tr>
          <td data-label="Name">
            <a href="{{ route('superadmin.admins.show', $admin) }}" style="font-weight:600;text-decoration:none;color:var(--text);">
              {{ $admin->name }}
            </a>
            @if($admin->business_type)
              <div class="text-muted" style="font-size:.78rem;">{{ $admin->business_type }}</div>
            @endif
            <span class="badge badge-custom" style="font-size:.68rem;margin-top:.2rem;{{ $admin->accountStatusBadgeStyle() }}">
              {{ $admin->accountStatusLabel() }}
            </span>
          </td>
          <td data-label="Email">
            <div>{{ $admin->email }}</div>
            @if($admin->phone)
              <div class="text-muted" style="font-size:.78rem;">{{ $admin->phone }}</div>
            @endif
          </td>
          <td data-label="Sub-Users">{{ $admin->sub_users_count }}</td>
          <td data-label="Clients">
            <a href="{{ route('superadmin.admins.clients', $admin) }}">
              {{ $admin->clients_count }} clients
            </a>
          </td>
          <td data-label="Plan / Expiry">
            @if($admin->plan_type)
              <div style="font-size:.85rem;font-weight:600;">{{ $admin->planLabel() }}</div>
              @if($admin->plan_type === 'lifetime')
                <div style="font-size:.75rem;color:var(--success);">Never expires</div>
              @elseif($admin->plan_expires_at)
                @php $days = $admin->daysUntilExpiry(); @endphp
                <div style="font-size:.75rem;color:{{ $days !== null && $days < 0 ? '#991b1b' : ($days !== null && $days <= 14 ? '#92400e' : 'var(--muted)') }};">
                  {{ $admin->plan_expires_at->format('d M Y') }}
                  @if($days !== null)
                    @if($days < 0) ({{ abs($days) }}d overdue)
                    @elseif($days <= 14) ({{ $days }}d left)
                    @endif
                  @endif
                </div>
              @endif
              @if($admin->is_suspended)
                <span style="font-size:.7rem;padding:.1rem .4rem;border-radius:8px;background:#fee2e2;color:#991b1b;font-weight:700;">Suspended</span>
              @endif
            @else
              <span class="text-muted" style="font-size:.82rem;">No plan</span>
            @endif
          </td>
          <td data-label="Joined">{{ $admin->created_at->format('d M Y') }}</td>
          <td data-label="Actions">
            <div class="d-flex gap-2">
              <a href="{{ route('superadmin.admins.show', $admin) }}" class="btn btn-sm btn-secondary">View</a>
              <a href="{{ route('superadmin.admins.edit', $admin) }}" class="btn btn-sm btn-primary">Edit</a>
              <form method="POST" action="{{ route('superadmin.admins.destroy', $admin) }}"
                    data-confirm="Delete admin &quot;{{ $admin->name }}&quot;? This permanently deletes all their clients, sub-users, and data.">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Del</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{ $admins->links('partials.pagination') }}
@endif
@endsection
