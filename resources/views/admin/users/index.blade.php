@extends('layouts.app')
@section('title', 'Users')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Users</h1>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ New User</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($users->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128101;</div>
    <p>No users yet. <a href="{{ route('admin.users.create') }}">Create the first user</a>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Joined</th>
          <th style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr>
          <td style="font-weight:600;">
            {{ $user->name }}
            @if($user->id === auth()->id())
              <span class="badge badge-custom" style="font-size:.7rem;margin-left:.25rem;">you</span>
            @endif
          </td>
          <td>{{ $user->email }}</td>
          <td>
            @forelse($user->roles as $role)
              <span class="badge badge-custom">{{ $role->name }}</span>
            @empty
              <span class="text-muted">—</span>
            @endforelse
          </td>
          <td>{{ $user->created_at->format('d M Y') }}</td>
          <td>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>
              @if($user->id !== auth()->id())
              <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                    data-confirm="Delete user &quot;{{ $user->name }}&quot;? This cannot be undone.">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Del</button>
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
{{ $users->links('partials.pagination') }}
@endif
@endsection
