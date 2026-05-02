@extends('layouts.app')
@section('title', 'Payment Submissions')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Payment Submissions</h1>
  <span class="text-muted" style="font-size:.9rem;">{{ $submissions->total() }} total</span>
</div>

@if(session('success'))
  <div class="alert alert-success" data-auto-dismiss>{{ session('success') }}</div>
@endif

{{-- Search --}}
<form method="GET" action="{{ route('superadmin.payments.index') }}" class="search-bar">
  <input class="form-control" type="text" name="search"
         placeholder="Search by name, email or phone…"
         value="{{ $search ?? '' }}">
  @if($status ?? null)<input type="hidden" name="status" value="{{ $status }}">@endif
  <button class="btn btn-secondary" type="submit">Search</button>
  @if(($search ?? '') || ($status ?? ''))
    <a href="{{ route('superadmin.payments.index') }}" class="btn btn-secondary">Clear</a>
  @endif
</form>

{{-- Status filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:1rem;">
  @php $base = array_filter(['search' => $search ?? null]); @endphp
  <a href="{{ route('superadmin.payments.index', $base) }}"
     class="btn btn-sm {{ !($status ?? null) ? 'btn-primary' : 'btn-secondary' }}">All</a>
  <a href="{{ route('superadmin.payments.index', array_merge($base, ['status'=>'pending'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'pending' ? 'btn-warning' : 'btn-secondary' }}">Pending</a>
  <a href="{{ route('superadmin.payments.index', array_merge($base, ['status'=>'active'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'active' ? 'btn-primary' : 'btn-secondary' }}">Approved</a>
  <a href="{{ route('superadmin.payments.index', array_merge($base, ['status'=>'rejected'])) }}"
     class="btn btn-sm {{ ($status ?? null) === 'rejected' ? 'btn-danger' : 'btn-secondary' }}">Rejected</a>
</div>

@if($submissions->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128184;</div>
    <p>No payment submissions found.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Admin</th>
          <th>Business</th>
          <th>Phone</th>
          <th>Plan</th>
          <th>Submitted</th>
          <th>Status</th>
          <th style="width:100px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($submissions as $admin)
        <tr>
          <td data-label="Admin">
            <div style="font-weight:600;">{{ $admin->name }}</div>
            <div class="text-muted" style="font-size:.78rem;">{{ $admin->email }}</div>
          </td>
          <td data-label="Business">{{ $admin->business_type ?? '—' }}</td>
          <td data-label="Phone">
            @if($admin->phone)
              <a href="tel:{{ $admin->phone }}">{{ $admin->phone }}</a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Plan">
            <span style="font-size:.85rem;font-weight:600;">{{ $admin->planLabel() }}</span>
          </td>
          <td data-label="Submitted">
            @if($admin->payment_submitted_at)
              <div>{{ $admin->payment_submitted_at->format('d M Y') }}</div>
              <div class="text-muted" style="font-size:.78rem;">{{ $admin->payment_submitted_at->format('H:i') }}</div>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Status">
            <span class="badge badge-custom" style="{{ $admin->accountStatusBadgeStyle() }}">
              {{ $admin->accountStatusLabel() }}
            </span>
          </td>
          <td data-label="Actions">
            <a href="{{ route('superadmin.payments.show', $admin) }}" class="btn btn-sm btn-primary">Review</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{ $submissions->links('partials.pagination') }}
@endif
@endsection
