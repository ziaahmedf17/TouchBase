@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('content')
<h2 class="auth-title">Forgot your password?</h2>
<p class="auth-subtitle">Enter your registered email and phone number to verify your identity.</p>

@if($errors->has('expired'))
  <div class="alert alert-danger" style="margin-bottom:1rem;">&#9888; {{ $errors->first('expired') }}</div>
@endif

<form method="POST" action="{{ route('password.forgot.submit') }}">
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
    <label class="form-label" for="phone">Registered phone number</label>
    <input type="tel"
           class="form-control @error('phone') is-error @enderror"
           id="phone"
           name="phone"
           value="{{ old('phone') }}"
           placeholder="+92300…"
           autocomplete="tel"
           required>
    @error('phone')
      <div class="form-error">{{ $message }}</div>
    @enderror
  </div>

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Verify &amp; Continue
  </button>
</form>

<p class="auth-switch">
  <a href="{{ route('login') }}">&larr; Back to sign in</a>
</p>
@endsection
