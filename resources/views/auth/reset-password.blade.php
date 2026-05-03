@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<h2 class="auth-title">Set new password</h2>
<p class="auth-subtitle">Choose a strong password for your account.</p>

<form method="POST" action="{{ route('password.reset.submit') }}">
  @csrf

  <div class="form-group">
    <label class="form-label" for="password">New password</label>
    <input type="password"
           class="form-control @error('password') is-error @enderror"
           id="password"
           name="password"
           autocomplete="new-password"
           autofocus
           required>
    @error('password')
      <div class="form-error">{{ $message }}</div>
    @enderror
    <div class="form-hint">Minimum 8 characters.</div>
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

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Reset Password
  </button>
</form>

<p class="auth-switch">
  <a href="{{ route('login') }}">&larr; Back to sign in</a>
</p>
@endsection
