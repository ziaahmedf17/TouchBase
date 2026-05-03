@extends('layouts.auth')
@section('title', 'Sign In')

@section('content')
<h2 class="auth-title">Welcome back</h2>
<p class="auth-subtitle">Sign in to your TouchBase account</p>

<form method="POST" action="{{ route('login') }}">
  @csrf

  <div class="form-group">
    <label class="form-label" for="email">Email address</label>
    <input type="email"
           class="form-control @error('email') is-error @enderror"
           id="email"
           name="email"
           value="{{ old('email') }}"
           autocomplete="email"
           autofocus
           required>
    @error('email')
      <div class="form-error">{{ $message }}</div>
    @enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="password">Password</label>
    <input type="password"
           class="form-control @error('password') is-error @enderror"
           id="password"
           name="password"
           autocomplete="current-password"
           required>
    @error('password')
      <div class="form-error">{{ $message }}</div>
    @enderror
  </div>

  <div class="form-group" style="display:flex;align-items:center;gap:.5rem;">
    <input type="checkbox" id="remember" name="remember" style="width:auto;">
    <label for="remember" style="margin:0;font-size:.875rem;font-weight:400;cursor:pointer;">Remember me</label>
  </div>

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Sign In
  </button>
</form>

<p class="auth-switch" style="margin-top:.75rem;">
  <a href="{{ route('password.forgot') }}" style="font-size:.85rem;">Forgot your password?</a>
</p>
<p class="auth-switch">
  Don't have an account? <a href="{{ route('register') }}">Create one</a>
</p>
@endsection
