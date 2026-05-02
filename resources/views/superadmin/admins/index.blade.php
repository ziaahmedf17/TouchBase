@extends('layouts.app')
@section('title', 'Admins')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Admins</h1>
  <a href="{{ route('superadmin.admins.create') }}" class="btn btn-primary">+ New Admin</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

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
          <th>Joined</th>
          <th style="width:150px;">Actions</th>
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
