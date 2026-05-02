@extends('layouts.app')
@section('title', 'Payment Submissions')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Payment Submissions</h1>
  <span class="text-muted" style="font-size:.9rem;">{{ $submissions->total() }} total</span>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($submissions->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128184;</div>
    <p>No payment submissions yet.</p>
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
          <th>Submitted</th>
          <th>Status</th>
          <th style="width:140px;">Actions</th>
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
            <div class="d-flex gap-2">
              <a href="{{ route('superadmin.payments.show', $admin) }}" class="btn btn-sm btn-secondary">View</a>
              @if($admin->account_status !== 'active')
              <form method="POST" action="{{ route('superadmin.payments.approve', $admin) }}"
                    data-confirm="Approve account for &quot;{{ $admin->name }}&quot;?">
                @csrf
                <button class="btn btn-sm btn-success">Approve</button>
              </form>
              @endif
            </div>
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
