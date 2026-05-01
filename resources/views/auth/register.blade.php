@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<h2 class="auth-title">Create account</h2>
<p class="auth-subtitle">Set up your TouchBase CRM</p>

<form method="POST" action="{{ route('register') }}">
  @csrf

  <div class="form-group">
    <label class="form-label" for="name">Full name</label>
    <input type="text"
           class="form-control @error('name') is-error @enderror"
           id="name"
           name="name"
           value="{{ old('name') }}"
           autocomplete="name"
           autofocus
           required>
    @error('name')
      <div class="form-error">{{ $message }}</div>
    @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="email">Email address</label>
    <input type="email"
           class="form-control @error('email') is-error @enderror"
           id="email"
           name="email"
           value="{{ old('email') }}"
           autocomplete="email"
           required>
    @error('email')
      <div class="form-error">{{ $message }}</div>
    @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="password">Password <span class="text-muted">(min. 8 characters)</span></label>
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
    <label class="form-label" for="password_confirmation">Confirm password</label>
    <input type="password"
           class="form-control"
           id="password_confirmation"
           name="password_confirmation"
           autocomplete="new-password"
           required>
  </div>

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Create Account
  </button>
</form>

<p class="auth-switch">
  Already have an account? <a href="{{ route('login') }}">Sign in</a>
</p>
@endsection
