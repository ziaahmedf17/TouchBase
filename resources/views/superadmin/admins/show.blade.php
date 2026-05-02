@extends('layouts.app')
@section('title', $admin->name)

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <div>
    <h1 class="page-title">{{ $admin->name }}</h1>
    <div class="text-muted" style="font-size:.85rem;">
      Admin &bull; Joined {{ $admin->created_at->format('d M Y') }}
    </div>
  </div>
  <div class="d-flex gap-2" style="flex-wrap:wrap;">
    @if($admin->is_suspended)
      <form method="POST" action="{{ route('superadmin.admins.unsuspend', $admin) }}">
        @csrf
        <button type="submit" class="btn btn-success">&#9654; Reactivate</button>
      </form>
    @else
      <form method="POST" action="{{ route('superadmin.admins.suspend', $admin) }}"
            data-confirm="Suspend account for &quot;{{ $admin->name }}&quot;? They will be redirected to the payment page.">
        @csrf
        <button type="submit" class="btn btn-danger">&#9646;&#9646; Suspend</button>
      </form>
    @endif
    <a href="{{ route('superadmin.admins.edit', $admin) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">&#8592; Back</a>
  </div>
</div>

@if(session('success'))
  <div class="alert alert-success" data-auto-dismiss>{{ session('success') }}</div>
@endif

{{-- Stats --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:1.25rem;">
  <div class="stat-card">
    <div class="num">{{ $admin->sub_users_count }}</div>
    <div class="label">Sub-Users</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $admin->clients_count }}</div>
    <div class="label">Clients</div>
  </div>
  <div class="stat-card">
    <div class="num">{{ $tickets->count() }}</div>
    <div class="label">Tickets</div>
  </div>
</div>

<div class="grid-2col">

  {{-- Profile Info --}}
  <div style="display:grid;gap:1rem;">

    <div class="card">
      <div class="card-title">Contact Information</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);width:40%;">Email</td>
          <td style="padding:.4rem 0;font-weight:500;">{{ $admin->email }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Phone</td>
          <td style="padding:.4rem 0;font-weight:500;">{{ $admin->phone ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Registered</td>
          <td style="padding:.4rem 0;">{{ $admin->created_at->format('d M Y, H:i') }}</td>
        </tr>
      </table>
    </div>

    <div class="card">
      <div class="card-title">Business Information</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);width:40%;vertical-align:top;">Type</td>
          <td style="padding:.4rem 0;font-weight:500;">{{ $admin->business_type ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);vertical-align:top;">Description</td>
          <td style="padding:.4rem 0;line-height:1.6;">{{ $admin->business_description ?? '—' }}</td>
        </tr>
      </table>
    </div>

    {{-- Sub-users --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
        <div class="card-title" style="margin:0;">Sub-Users ({{ $admin->sub_users_count }})</div>
      </div>
      @forelse($admin->subUsers as $user)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);">
          <div>
            <div style="font-size:.9rem;font-weight:500;">{{ $user->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $user->email }}</div>
          </div>
          <span class="badge badge-custom" style="font-size:.72rem;">
            {{ $user->roles->first()?->name ?? 'No role' }}
          </span>
        </div>
      @empty
        <p class="text-muted" style="font-size:.85rem;margin:0;">No sub-users yet.</p>
      @endforelse
    </div>

  </div>

  {{-- Right column --}}
  <div style="display:grid;gap:1rem;align-content:start;">

    {{-- Subscription & Status --}}
    <div class="card">
      <div class="card-title">Subscription</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;margin-bottom:1rem;">
        <tr>
          <td style="padding:.35rem 0;color:var(--muted);width:45%;">Status</td>
          <td style="padding:.35rem 0;">
            <span style="padding:.2rem .6rem;border-radius:12px;font-size:.78rem;font-weight:600;{{ $admin->accountStatusBadgeStyle() }}">
              {{ $admin->accountStatusLabel() }}
            </span>
            @if($admin->is_suspended)
              <span style="padding:.2rem .6rem;border-radius:12px;font-size:.78rem;font-weight:600;background:#fee2e2;color:#991b1b;margin-left:.3rem;">
                Suspended
              </span>
            @endif
          </td>
        </tr>
        <tr>
          <td style="padding:.35rem 0;color:var(--muted);">Plan</td>
          <td style="padding:.35rem 0;font-weight:600;">{{ $admin->planLabel() }}</td>
        </tr>
        @if($admin->plan_started_at)
        <tr>
          <td style="padding:.35rem 0;color:var(--muted);">Started</td>
          <td style="padding:.35rem 0;">{{ $admin->plan_started_at->format('d M Y') }}</td>
        </tr>
        @endif
        @if($admin->plan_expires_at)
        <tr>
          <td style="padding:.35rem 0;color:var(--muted);">Expires</td>
          <td style="padding:.35rem 0;">
            {{ $admin->plan_expires_at->format('d M Y') }}
            @php $days = $admin->daysUntilExpiry(); @endphp
            @if($days !== null)
              @if($days >= 0)
                <span style="font-size:.75rem;color:{{ $days <= 14 ? '#92400e' : 'var(--muted)' }};">
                  ({{ $days }}d left)
                </span>
              @else
                <span style="font-size:.75rem;color:#991b1b;">({{ abs($days) }}d overdue)</span>
              @endif
            @endif
          </td>
        </tr>
        @elseif($admin->plan_type === 'lifetime')
        <tr>
          <td style="padding:.35rem 0;color:var(--muted);">Expires</td>
          <td style="padding:.35rem 0;color:var(--success);font-weight:600;">Never</td>
        </tr>
        @endif
      </table>

      {{-- Set plan form --}}
      <form method="POST" action="{{ route('superadmin.admins.setPlan', $admin) }}">
        @csrf
        <div style="display:flex;gap:.5rem;align-items:center;">
          <select name="plan_type" class="form-control" style="font-size:.85rem;flex:1;">
            @foreach(['monthly','yearly','lifetime'] as $slug)
              @php $p = $plans[$slug] ?? null; @endphp
              @if($p)
                <option value="{{ $p->slug }}" {{ $admin->plan_type === $p->slug ? 'selected' : '' }}>
                  {{ $p->name }} — {{ $p->formattedPrice() }}
                </option>
              @endif
            @endforeach
          </select>
          <button type="submit" class="btn btn-primary" style="white-space:nowrap;font-size:.82rem;">Set Plan</button>
        </div>
      </form>
    </div>

    {{-- Recent Clients --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
        <div class="card-title" style="margin:0;">Recent Clients</div>
        <a href="{{ route('superadmin.admins.clients', $admin) }}" style="font-size:.82rem;">
          View all ({{ $admin->clients_count }})
        </a>
      </div>
      @forelse($recentClients as $client)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);">
          <div style="font-size:.9rem;font-weight:500;">{{ $client->name }}</div>
          <div class="text-muted" style="font-size:.78rem;">{{ $client->phone ?? '—' }}</div>
        </div>
      @empty
        <p class="text-muted" style="font-size:.85rem;margin:0;">No clients yet.</p>
      @endforelse
    </div>

    {{-- Recent Tickets --}}
    <div class="card">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
        <div class="card-title" style="margin:0;">Recent Tickets</div>
        <a href="{{ route('superadmin.tickets.index') }}" style="font-size:.82rem;">View all</a>
      </div>
      @forelse($tickets as $ticket)
        <a href="{{ route('superadmin.tickets.show', $ticket) }}"
           style="display:flex;align-items:center;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border);text-decoration:none;color:inherit;">
          <div>
            <div style="font-size:.85rem;font-weight:500;">{{ $ticket->ticket_number }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ Str::limit($ticket->subject, 40) }}</div>
          </div>
          <span class="badge badge-custom" style="font-size:.72rem;{{ $ticket->statusBadgeStyle() }}">
            {{ $ticket->statusLabel() }}
          </span>
        </a>
      @empty
        <p class="text-muted" style="font-size:.85rem;margin:0;">No tickets yet.</p>
      @endforelse
    </div>

  </div>

</div>
@endsection
