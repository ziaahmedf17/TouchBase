@extends('layouts.app')
@section('title', 'Roles')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Roles</h1>
  <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">+ New Role</a>
</div>

@if($roles->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128100;</div>
    <p>No roles yet. <a href="{{ route('admin.roles.create') }}">Create your first role</a>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Slug</th>
          <th>Description</th>
          <th>Permissions</th>
          <th>Users</th>
          <th style="width:120px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($roles as $role)
        <tr>
          <td style="font-weight:600;">{{ $role->name }}</td>
          <td><code style="font-size:.8rem;background:var(--surface);padding:.1rem .35rem;border-radius:4px;">{{ $role->slug }}</code></td>
          <td>{{ $role->description ?? '—' }}</td>
          <td>
            @forelse($role->permissions->take(4) as $perm)
              <span class="badge badge-custom" style="margin:.1rem .1rem 0 0;">{{ $perm->slug }}</span>
            @empty
              <span class="text-muted">none</span>
            @endforelse
            @if($role->permissions->count() > 4)
              <span class="text-muted" style="font-size:.8rem;">+{{ $role->permissions->count() - 4 }} more</span>
            @endif
          </td>
          <td>{{ $role->users_count }}</td>
          <td>
            <div class="d-flex gap-2">
              <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-primary">Edit</a>
              @if($role->slug !== 'admin')
              <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                    data-confirm="Delete role &quot;{{ $role->name }}&quot;? This will remove it from {{ $role->users_count }} user(s).">
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
@endif
@endsection
