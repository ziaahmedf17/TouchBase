@extends('layouts.app')
@section('title', 'Activity Log')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">Activity Log</h1>
  <span class="text-muted" style="font-size:.9rem;">{{ $logs->total() }} entries</span>
</div>

{{-- Search + filter --}}
<form method="GET" action="{{ route('superadmin.activity.index') }}" class="search-bar">
  <input class="form-control" type="text" name="search"
         placeholder="Search descriptions…"
         value="{{ $search ?? '' }}">
  @if($action ?? null)<input type="hidden" name="action" value="{{ $action }}">@endif
  <button class="btn btn-secondary" type="submit">Search</button>
  @if(($search ?? '') || ($action ?? ''))
    <a href="{{ route('superadmin.activity.index') }}" class="btn btn-secondary">Clear</a>
  @endif
</form>

{{-- Action filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:1rem;">
  @php $base = array_filter(['search' => $search ?? null]); @endphp
  <a href="{{ route('superadmin.activity.index', $base) }}"
     class="btn btn-sm {{ !($action ?? null) ? 'btn-primary' : 'btn-secondary' }}">All</a>
  @foreach([
    'payment_approved'  => 'Approved',
    'payment_rejected'  => 'Rejected',
    'admin_suspended'   => 'Suspended',
    'admin_unsuspended' => 'Reactivated',
    'plan_set'          => 'Plan Set',
    'price_updated'     => 'Price Updated',
  ] as $val => $label)
  <a href="{{ route('superadmin.activity.index', array_merge($base, ['action' => $val])) }}"
     class="btn btn-sm {{ ($action ?? null) === $val ? 'btn-primary' : 'btn-secondary' }}">{{ $label }}</a>
  @endforeach
</div>

@if($logs->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128221;</div>
    <p>No activity logged yet. Actions will appear here once taken.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Action</th>
          <th>Description</th>
          <th>By</th>
          <th>When</th>
        </tr>
      </thead>
      <tbody>
        @foreach($logs as $log)
        <tr>
          <td data-label="Action">
            <span class="badge {{ $log->actionBadgeClass() }}" style="font-size:.75rem;">
              {{ $log->actionLabel() }}
            </span>
          </td>
          <td data-label="Description" style="font-size:.9rem;">
            {{ $log->description }}
          </td>
          <td data-label="By">
            <div style="font-size:.88rem;font-weight:500;">{{ $log->causer?->name ?? 'System' }}</div>
            <div class="text-muted" style="font-size:.75rem;">{{ $log->causer?->email }}</div>
          </td>
          <td data-label="When" style="white-space:nowrap;">
            <div style="font-size:.88rem;">{{ $log->created_at->format('d M Y') }}</div>
            <div class="text-muted" style="font-size:.75rem;">
              {{ $log->created_at->format('H:i') }} &bull; {{ $log->created_at->diffForHumans() }}
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{ $logs->links('partials.pagination') }}
@endif
@endsection
