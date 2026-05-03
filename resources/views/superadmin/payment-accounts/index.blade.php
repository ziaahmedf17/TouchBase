@extends('layouts.app')
@section('title', 'Bank Accounts')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Bank Accounts</h1>
  <a href="{{ route('superadmin.payment-accounts.create') }}" class="btn btn-primary">+ Add Account</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($accounts->isEmpty())
  <div class="empty-state">
    <div class="icon">&#127974;</div>
    <p>No bank accounts added yet. <a href="{{ route('superadmin.payment-accounts.create') }}">Add your first account</a>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Title</th>
          <th>Bank</th>
          <th>Account Number</th>
          <th>Account Holder</th>
          <th>Status</th>
          <th style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($accounts as $account)
        <tr>
          <td data-label="Title" style="font-weight:600;">{{ $account->title }}</td>
          <td data-label="Bank">{{ $account->bank_name }}</td>
          <td data-label="Account Number">
            <span style="font-family:monospace;">{{ $account->account_number }}</span>
            @if($account->iban)
              <div class="text-muted" style="font-size:.78rem;font-family:monospace;">{{ $account->iban }}</div>
            @endif
          </td>
          <td data-label="Account Holder">{{ $account->account_holder }}</td>
          <td data-label="Status">
            @if($account->is_active)
              <span class="badge badge-success">Active</span>
            @else
              <span class="badge badge-neutral">Inactive</span>
            @endif
          </td>
          <td data-label="Actions">
            <div class="d-flex gap-2">
              <a href="{{ route('superadmin.payment-accounts.edit', $account) }}" class="btn btn-sm btn-primary">Edit</a>
              <form method="POST" action="{{ route('superadmin.payment-accounts.destroy', $account) }}"
                    data-confirm="Remove bank account &quot;{{ $account->title }}&quot;?">
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
@endif
@endsection
