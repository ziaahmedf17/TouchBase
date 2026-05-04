@extends('layouts.app')
@section('title', 'New User')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">New User</h1>
  <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}" placeholder="Full name" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Email <span style="color:var(--danger)">*</span></label>
      <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
             value="{{ old('email') }}" placeholder="email@example.com" required>
      @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Password <span style="color:var(--danger)">*</span></label>
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
             placeholder="Min. 8 characters" required>
      @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Confirm Password <span style="color:var(--danger)">*</span></label>
      <input type="password" name="password_confirmation" class="form-control" required>
    </div>

    <div class="form-group">
      <label class="form-label">Role <span style="color:var(--danger)">*</span></label>
      <select name="role" class="form-control @error('role') is-invalid @enderror" required>
        <option value="">Select a role</option>
        @foreach($roles as $role)
          <option value="{{ $role->id }}" {{ old('role') == $role->id ? 'selected' : '' }}>
            {{ $role->name }}
          </option>
        @endforeach
      </select>
      @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Create User</button>
      <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
