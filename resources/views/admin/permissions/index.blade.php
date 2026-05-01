@extends('layouts.app')
@section('title', 'Permissions')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Permissions</h1>
  <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">+ New Permission</a>
</div>

@if($permissions->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128274;</div>
    <p>No permissions yet. <a href="{{ route('admin.permissions.create') }}">Create your first permission</a>.</p>
  </div>
@else
  @foreach($permissions as $group => $perms)
  <div class="card" style="margin-bottom:1.25rem;padding:0;">
    <div style="padding:.75rem 1.25rem;border-bottom:1px solid var(--border);font-weight:600;font-size:.9rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">
      {{ $group }}
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Slug</th>
            <th>Description</th>
            <th style="width:100px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($perms as $perm)
          <tr>
            <td style="font-weight:500;">{{ $perm->name }}</td>
            <td><code style="font-size:.8rem;background:var(--surface);padding:.1rem .35rem;border-radius:4px;">{{ $perm->slug }}</code></td>
            <td>{{ $perm->description ?? '—' }}</td>
            <td>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.permissions.edit', $perm) }}" class="btn btn-sm btn-primary">Edit</a>
                <form method="POST" action="{{ route('admin.permissions.destroy', $perm) }}"
                      data-confirm="Delete permission &quot;{{ $perm->name }}&quot;? This will remove it from all roles.">
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
  @endforeach
@endif
@endsection
