@extends('layouts.app')
@section('title', 'Edit Admin')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Edit Admin: {{ $admin->name }}</h1>
  <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('superadmin.admins.update', $admin) }}">
    @csrf @method('PUT')

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $admin->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Email <span style="color:var(--danger)">*</span></label>
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email', $admin->email) }}" required>
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">New Password <span class="text-muted" style="font-weight:400;">(leave blank to keep current)</span></label>
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
             placeholder="Min. 8 characters">
      @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Confirm New Password</label>
      <input type="password" name="password_confirmation" class="form-control">
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
