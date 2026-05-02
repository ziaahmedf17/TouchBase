@extends('layouts.auth')
@section('title', 'Pending Approval')

@section('content')
<div style="text-align:center;">

  <div style="font-size:3rem;margin-bottom:1rem;">&#9203;</div>

  <h2 class="auth-title">Payment Under Review</h2>
  <p class="auth-subtitle" style="margin-bottom:1.5rem;">Your account registration is almost complete</p>

  <div class="card" style="text-align:left;margin-bottom:1.25rem;">
    <div style="font-size:.95rem;line-height:1.7;color:var(--text);">
      After payment verification, your account will be approved. Our team will contact you on your
      registered contact number and inform you within a few hours.
    </div>
    @if(Auth::user()->phone)
    <div style="margin-top:.75rem;padding:.6rem .85rem;background:var(--bg);border-radius:6px;font-size:.875rem;">
      <span class="text-muted">We will call you on:</span>
      <strong style="margin-left:.4rem;">{{ Auth::user()->phone }}</strong>
    </div>
    @endif
  </div>

  <div style="display:grid;gap:.5rem;font-size:.85rem;text-align:left;margin-bottom:1.5rem;">
    <div style="display:flex;align-items:center;gap:.6rem;color:var(--success);">
      <span style="font-size:1rem;">&#10003;</span> Account information submitted
    </div>
    <div style="display:flex;align-items:center;gap:.6rem;color:var(--success);">
      <span style="font-size:1rem;">&#10003;</span> Payment screenshot uploaded
    </div>
    <div style="display:flex;align-items:center;gap:.6rem;color:var(--warning);">
      <span style="font-size:1rem;">&#9679;</span> Awaiting payment verification
    </div>
    <div style="display:flex;align-items:center;gap:.6rem;color:var(--muted);">
      <span style="font-size:1rem;">&#9675;</span> Account activation
    </div>
  </div>

  <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;margin-bottom:.75rem;">
    Check Account Status
  </a>

  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;padding:.55rem;">
      Sign Out
    </button>
  </form>

</div>
@endsection
