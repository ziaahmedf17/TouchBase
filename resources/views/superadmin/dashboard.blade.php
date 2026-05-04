@extends('layouts.app')
@section('title', 'Platform Overview')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">Platform Overview</h1>
  <span class="text-muted" style="font-size:.85rem;">{{ now()->format('d M Y') }}</span>
</div>

{{-- ── Main stats ───────────────────────────── --}}
<div class="stats-grid" style="grid-template-columns:repeat(auto-fill,minmax(150px,1fr));margin-bottom:1.25rem;">
  <a href="{{ route('superadmin.admins.index') }}" class="stat-card">
    <div class="num">{{ $stats['total_admins'] }}</div>
    <div class="label">Total Admins</div>
  </a>
  <a href="{{ route('superadmin.admins.index', ['status' => 'active']) }}" class="stat-card">
    <div class="num" style="color:var(--success);">{{ $stats['active_admins'] }}</div>
    <div class="label">Active</div>
  </a>
  <div class="stat-card">
    <div class="num" style="color:#2563eb;">{{ $stats['trial_admins'] }}</div>
    <div class="label">On Trial</div>
  </div>
  <a href="{{ route('superadmin.payments.index', ['status' => 'pending']) }}" class="stat-card">
    <div class="num" style="color:var(--warning);">{{ $stats['pending_admins'] }}</div>
    <div class="label">Pending Approval</div>
  </a>
  <a href="{{ route('superadmin.admins.index', ['status' => 'suspended']) }}" class="stat-card">
    <div class="num" style="color:var(--danger);">{{ $stats['suspended_admins'] }}</div>
    <div class="label">Suspended</div>
  </a>
  <div class="stat-card">
    <div class="num">{{ $stats['total_clients'] }}</div>
    <div class="label">Total Clients</div>
  </div>
  <a href="{{ route('superadmin.tickets.index') }}" class="stat-card">
    <div class="num" style="color:var(--danger);">{{ $stats['open_tickets'] }}</div>
    <div class="label">Open Tickets</div>
  </a>
  <a href="{{ route('superadmin.contacts.index') }}" class="stat-card" style="{{ $stats['unread_inquiries'] > 0 ? 'border-top:3px solid var(--primary);' : '' }}">
    <div class="num" style="{{ $stats['unread_inquiries'] > 0 ? 'color:var(--primary);' : '' }}">
      {{ $stats['unread_inquiries'] }}
    </div>
    <div class="label">Unread Inquiries</div>
  </a>
</div>

{{-- ── Action required ─────────────────────── --}}
@if($pendingApprovals->isNotEmpty() || $suspendedAdmins->isNotEmpty())
<div class="grid-2col" style="margin-bottom:1.25rem;">

  {{-- Pending approvals --}}
  <div class="card" style="border-left:4px solid var(--warning);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;color:var(--warning);">
        &#9888; Pending Approvals
        <span style="font-size:.75rem;font-weight:400;margin-left:.4rem;color:var(--muted);">({{ $pendingApprovals->count() }})</span>
      </div>
      <a href="{{ route('superadmin.payments.index', ['status'=>'pending']) }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($pendingApprovals as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $admin->name }}</div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->email }}
          @if($admin->phone) &bull; <a href="tel:{{ $admin->phone }}" style="color:inherit;">{{ $admin->phone }}</a> @endif
        </div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->business_type ?? '—' }}
          &bull; {{ $admin->planLabel() }}
          @if($admin->payment_submitted_at) &bull; {{ $admin->payment_submitted_at->diffForHumans() }} @endif
        </div>
      </div>
      <a href="{{ route('superadmin.payments.show', $admin) }}"
         class="btn btn-sm btn-warning" style="white-space:nowrap;margin-left:.75rem;">Review</a>
    </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No pending approvals.</p>
    @endforelse
  </div>

  {{-- Suspended admins --}}
  <div class="card" style="border-left:4px solid var(--danger);">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;color:var(--danger);">
        &#128274; Suspended Admins
        <span style="font-size:.75rem;font-weight:400;margin-left:.4rem;color:var(--muted);">({{ $suspendedAdmins->count() }})</span>
      </div>
      <a href="{{ route('superadmin.admins.index', ['status'=>'suspended']) }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($suspendedAdmins as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $admin->name }}</div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->email }}
          @if($admin->phone) &bull; <a href="tel:{{ $admin->phone }}" style="color:inherit;">{{ $admin->phone }}</a> @endif
        </div>
        <div class="text-muted" style="font-size:.75rem;">
          {{ $admin->planLabel() }}
          @if($admin->plan_expires_at) &bull; expired {{ $admin->plan_expires_at->format('d M Y') }} @endif
        </div>
      </div>
      <div class="d-flex gap-2" style="margin-left:.75rem;">
        <form method="POST" action="{{ route('superadmin.admins.unsuspend', $admin) }}">
          @csrf
          <button type="submit" class="btn btn-sm btn-success" style="white-space:nowrap;">Reactivate</button>
        </form>
        <a href="{{ route('superadmin.admins.show', $admin) }}" class="btn btn-sm btn-secondary">View</a>
      </div>
    </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No suspended admins.</p>
    @endforelse
  </div>

</div>
@endif

{{-- ── Plan distribution + upcoming renewals ── --}}
<div class="grid-2col" style="margin-bottom:1.25rem;">

  {{-- Plan breakdown + revenue --}}
  <div class="card">
    <div class="card-title">Active Subscriptions</div>
    <div style="display:grid;gap:.75rem;">
      @foreach(['monthly','yearly','lifetime'] as $slug)
        @php
          $p    = $plans[$slug] ?? null;
          $row  = $revenueSummary[$slug] ?? ['count' => 0, 'price' => 0, 'value' => 0];
        @endphp
        @if($p)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .75rem;background:var(--bg);border-radius:var(--radius);">
          <div>
            <div style="font-weight:600;font-size:.9rem;">{{ $p->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $p->formattedPrice() }}
              @if($slug !== 'lifetime') / {{ $slug === 'monthly' ? 'mo' : 'yr' }} @endif
            </div>
          </div>
          <div style="text-align:right;">
            <div style="font-size:1.4rem;font-weight:700;line-height:1;">{{ $row['count'] }}</div>
            <div class="text-muted" style="font-size:.72rem;">admin{{ $row['count'] == 1 ? '' : 's' }}</div>
            @if($row['value'] > 0)
            <div style="font-size:.72rem;color:var(--success);font-weight:600;margin-top:.1rem;">
              Rs. {{ number_format($row['value'], 0) }}
            </div>
            @endif
          </div>
        </div>
        @endif
      @endforeach

      {{-- Trial row --}}
      @if(($planCounts['trial'] ?? 0) > 0)
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.6rem .75rem;background:#eff6ff;border-radius:var(--radius);border:1px solid #bfdbfe;">
        <div>
          <div style="font-weight:600;font-size:.9rem;color:#1e40af;">Free Trial</div>
          <div style="font-size:.78rem;color:#3b82f6;">14-day free access</div>
        </div>
        <div style="text-align:right;">
          <div style="font-size:1.4rem;font-weight:700;line-height:1;color:#2563eb;">{{ $planCounts['trial'] }}</div>
          <div style="font-size:.72rem;color:#3b82f6;">admin{{ $planCounts['trial'] == 1 ? '' : 's' }}</div>
        </div>
      </div>
      @endif

      {{-- Total value --}}
      @if(($revenueSummary['total'] ?? 0) > 0)
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.55rem .75rem;border-top:2px solid var(--border);margin-top:.25rem;">
        <div style="font-size:.85rem;font-weight:600;color:var(--muted);">Total Active Value</div>
        <div style="font-size:1.05rem;font-weight:700;color:var(--success);">
          Rs. {{ number_format($revenueSummary['total'], 0) }}
        </div>
      </div>
      @endif
    </div>
  </div>

  {{-- Upcoming renewals --}}
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Renewals &amp; Expiring Trials</div>
      <span style="font-size:.8rem;padding:.15rem .5rem;border-radius:10px;background:var(--bg);color:var(--muted);">
        {{ $upcomingRenewals->count() }}
      </span>
    </div>
    @forelse($upcomingRenewals as $admin)
      @php $days = (int) now()->diffInDays($admin->plan_expires_at, false); @endphp
      <div style="display:flex;align-items:center;justify-content:space-between;padding:.45rem 0;border-bottom:1px solid var(--border);">
        <div>
          <div style="font-size:.88rem;font-weight:600;display:flex;align-items:center;gap:.4rem;">
            <a href="{{ route('superadmin.admins.show', $admin) }}" style="text-decoration:none;color:inherit;">{{ $admin->name }}</a>
            @if($admin->plan_type === 'trial')
              <span style="font-size:.65rem;background:#dbeafe;color:#1e40af;padding:.1rem .4rem;border-radius:6px;font-weight:700;">TRIAL</span>
            @endif
          </div>
          <div class="text-muted" style="font-size:.75rem;">{{ $admin->planLabel() }} &bull; expires {{ $admin->plan_expires_at->format('d M Y') }}</div>
        </div>
        <span class="{{ $days <= 7 ? 'renewal-days-danger' : 'renewal-days-warning' }}">
          {{ $days }}d
        </span>
      </div>
    @empty
      <p class="text-muted" style="font-size:.85rem;margin:0;">No renewals due in the next 30 days.</p>
    @endforelse
  </div>

</div>

{{-- ── Announcement Banner ──────────────────── --}}
@php $activeAnnouncement = \App\Models\Announcement::where('is_active', true)->latest()->first(); @endphp
<div class="card" style="margin-bottom:1.25rem;">
  <div class="card-title">&#128226; Announcement Banner</div>
  <p class="text-muted" style="font-size:.85rem;margin-bottom:.75rem;">
    Publish a message that appears as a banner on all admin dashboards.
  </p>
  @if($activeAnnouncement)
  <div class="announcement-preview">
    <div>
      <div class="announcement-preview-title">Active Announcement</div>
      <div class="announcement-preview-body">{{ $activeAnnouncement->message }}</div>
      <div class="announcement-preview-meta">Published {{ $activeAnnouncement->created_at->diffForHumans() }}</div>
    </div>
    <form method="POST" action="{{ route('superadmin.announcement.destroy') }}">
      @csrf @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger">Clear Banner</button>
    </form>
  </div>
  @endif
  <form method="POST" action="{{ route('superadmin.announcement.store') }}" style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:flex-start;">
    @csrf
    <input type="text" name="message" class="form-control" style="flex:1;min-width:220px;"
           placeholder="{{ $activeAnnouncement ? 'Replace with new message…' : 'Write an announcement…' }}"
           maxlength="500" required>
    <button type="submit" class="btn btn-primary" style="white-space:nowrap;">
      {{ $activeAnnouncement ? 'Replace' : 'Publish' }}
    </button>
  </form>
  @error('message')<div class="form-error mt-1">{{ $message }}</div>@enderror
</div>

{{-- ── Tools ───────────────────────────────── --}}
<div class="card" style="margin-bottom:1.25rem;">
  <div class="card-title">System Tools</div>
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
    <div>
      <div style="font-size:.9rem;font-weight:600;">Clear Cache</div>
      <div class="text-muted" style="font-size:.8rem;">Clears config, route, view, and application cache. Use after deploying updates.</div>
    </div>
    <form method="POST" action="{{ route('superadmin.cache.clear') }}">
      @csrf
      <button type="submit" class="btn btn-secondary">&#128465; Clear Cache</button>
    </form>
  </div>
</div>

{{-- ── Recent inquiries ────────────────────── --}}
@if($recentInquiries->isNotEmpty())
<div class="card" style="margin-bottom:1.25rem;">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
    <div class="card-title" style="margin:0;">&#9993; Recent Contact Inquiries</div>
    <a href="{{ route('superadmin.contacts.index') }}" style="font-size:.82rem;">View all</a>
  </div>
  <div style="display:grid;gap:.5rem;">
    @foreach($recentInquiries as $inq)
    <div style="padding:.65rem .75rem;background:var(--bg);border-radius:var(--radius);
                {{ !$inq->is_read ? 'border-left:3px solid var(--primary);' : '' }}">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:0;">
          <div style="font-weight:600;font-size:.9rem;display:flex;align-items:center;gap:.5rem;">
            {{ $inq->name }}
            @if(!$inq->is_read)
              <span style="font-size:.68rem;background:var(--primary);color:#fff;padding:.1rem .4rem;border-radius:8px;">New</span>
            @endif
          </div>
          <div class="text-muted" style="font-size:.78rem;">
            <a href="mailto:{{ $inq->email }}" style="color:var(--muted);">{{ $inq->email }}</a>
            @if($inq->phone) &bull; {{ $inq->phone }} @endif
            @if($inq->subject) &bull; <em>{{ $inq->subject }}</em> @endif
          </div>
          <div style="font-size:.82rem;margin-top:.3rem;color:var(--text);">{{ Str::limit($inq->message, 120) }}</div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0;">
          <div style="font-size:.75rem;color:var(--muted);white-space:nowrap;">
            {{ $inq->created_at->diffForHumans() }}
          </div>
          <a href="{{ route('superadmin.contacts.show', $inq) }}" class="btn btn-sm btn-primary">View</a>
        </div>
      </div>
    </div>
    @endforeach
  </div>
</div>
@endif

{{-- ── Recent admins + pending tickets ────── --}}
<div class="grid-2col">

  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Recent Admins</div>
      <a href="{{ route('superadmin.admins.index') }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($recentAdmins as $admin)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);">
      <div>
        <div style="font-weight:600;font-size:.9rem;">
          <a href="{{ route('superadmin.admins.show', $admin) }}" style="text-decoration:none;color:inherit;">{{ $admin->name }}</a>
        </div>
        <div class="text-muted" style="font-size:.78rem;">{{ $admin->email }}</div>
      </div>
      <div style="text-align:right;">
        <span class="badge {{ $admin->accountStatusBadgeClass() }}" style="font-size:.68rem;">
          {{ $admin->accountStatusLabel() }}
        </span>
        <div class="text-muted" style="font-size:.72rem;margin-top:.2rem;">{{ $admin->created_at->format('d M Y') }}</div>
      </div>
    </div>
    @empty
      <p class="text-muted">No admins yet.</p>
    @endforelse
  </div>

  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <div class="card-title" style="margin:0;">Pending Tickets</div>
      <a href="{{ route('superadmin.tickets.index') }}" style="font-size:.82rem;">View all</a>
    </div>
    @forelse($recentTickets as $ticket)
    <a href="{{ route('superadmin.tickets.show', $ticket) }}"
       style="display:flex;align-items:center;justify-content:space-between;padding:.5rem 0;border-bottom:1px solid var(--border);text-decoration:none;color:inherit;">
      <div>
        <div style="font-weight:600;font-size:.9rem;">{{ $ticket->ticket_number }}</div>
        <div class="text-muted" style="font-size:.78rem;">
          {{ $ticket->user->name }} &bull; {{ Str::limit($ticket->subject, 38) }}
        </div>
      </div>
      <span class="badge {{ $ticket->statusBadgeClass() }}" style="white-space:nowrap;margin-left:.75rem;">
        {{ $ticket->statusLabel() }}
      </span>
    </a>
    @empty
      <p class="text-muted">No pending tickets.</p>
    @endforelse
  </div>

</div>
@endsection
