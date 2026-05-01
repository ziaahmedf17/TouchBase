@extends('layouts.app')
@section('title', 'Change Password')

@section('content')
<div style="max-width:480px;margin:0 auto;">
  <div class="page-header">
    <h1 class="page-title">Change Password</h1>
  </div>

  <div class="card">
    <form method="POST" action="{{ route('password.update') }}">
      @csrf @method('PUT')

      <div class="form-group">
        <label class="form-label" for="current_password">Current password</label>
        <input type="password"
               class="form-control @error('current_password') is-error @enderror"
               id="current_password"
               name="current_password"
               autocomplete="current-password"
               required>
        @error('current_password')
          <div class="form-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">New password <span class="text-muted">(min. 8 characters)</span></label>
        <input type="password"
               class="form-control @error('password') is-error @enderror"
               id="password"
               name="password"
               autocomplete="new-password"
               required>
        @error('password')
          <div class="form-error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password_confirmation">Confirm new password</label>
        <input type="password"
               class="form-control"
               id="password_confirmation"
               name="password_confirmation"
               autocomplete="new-password"
               required>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Password</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
