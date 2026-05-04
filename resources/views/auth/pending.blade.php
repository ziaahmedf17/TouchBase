@extends('layouts.auth')
@section('title', 'Account Status')

@section('content')
@php $user = Auth::user(); @endphp

{{-- ── REJECTED STATE ── --}}
@if($user->account_status === 'rejected')

<div style="text-align:center;margin-bottom:1.25rem;">
  <div style="font-size:2.5rem;">&#10060;</div>
  <h2 class="auth-title" style="color:var(--danger);">Payment Not Verified</h2>
  <p class="auth-subtitle">Your payment could not be confirmed. Please resubmit a clear screenshot.</p>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('contact_updated'))
  <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; Contact information updated.</div>
@endif

{{-- Payment accounts --}}
@if($accounts->isNotEmpty())
<div style="margin-bottom:1.25rem;">
  @foreach($accounts as $account)
  <div class="card" style="padding:.85rem 1rem;margin-bottom:.6rem;">
    <div style="font-weight:700;font-size:.9rem;margin-bottom:.5rem;">{{ $account->title }}</div>
    <table style="width:100%;font-size:.82rem;border-collapse:collapse;">
      <tr>
        <td style="color:var(--muted);padding:.2rem 0;width:40%;">Bank</td>
        <td style="font-weight:500;">{{ $account->bank_name }}</td>
      </tr>
      <tr>
        <td style="color:var(--muted);padding:.2rem 0;">Account Title</td>
        <td style="font-weight:500;">{{ $account->account_holder }}</td>
      </tr>
      <tr>
        <td style="color:var(--muted);padding:.2rem 0;">Account No.</td>
        <td>
          <span style="font-weight:600;font-family:monospace;">{{ $account->account_number }}</span>
          <button type="button" class="btn btn-sm btn-secondary"
                  style="margin-left:.4rem;padding:.1rem .45rem;font-size:.68rem;"
                  data-copy="{{ $account->account_number }}">Copy</button>
        </td>
      </tr>
      @if($account->iban)
      <tr>
        <td style="color:var(--muted);padding:.2rem 0;">IBAN</td>
        <td>
          <span style="font-weight:600;font-family:monospace;font-size:.78rem;">{{ $account->iban }}</span>
          <button type="button" class="btn btn-sm btn-secondary"
                  style="margin-left:.4rem;padding:.1rem .45rem;font-size:.68rem;"
                  data-copy="{{ $account->iban }}">Copy</button>
        </td>
      </tr>
      @endif
    </table>
    @if($account->instructions)
      <div style="margin-top:.5rem;padding:.4rem .6rem;background:var(--bg);border-radius:5px;font-size:.78rem;color:var(--muted);">
        {{ $account->instructions }}
      </div>
    @endif
  </div>
  @endforeach
</div>
@endif

{{-- Resubmit screenshot --}}
<form method="POST" action="{{ route('account.resubmit') }}" enctype="multipart/form-data"
      style="margin-bottom:1.25rem;">
  @csrf
  <div class="form-group">
    <label class="form-label">Upload new payment screenshot
      <span class="text-muted" style="font-weight:400;font-size:.8rem;">(JPG, PNG, PDF, max 5 MB)</span>
    </label>
    <input type="file" name="screenshot"
           class="form-control @error('screenshot') is-error @enderror"
           accept=".jpg,.jpeg,.png,.gif,.pdf" required>
    @error('screenshot')<div class="form-error">{{ $message }}</div>@enderror
  </div>
  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
    Resubmit Payment Screenshot
  </button>
</form>

<hr style="border:none;border-top:1px solid var(--border);margin:1.25rem 0;">

{{-- Update contact info --}}
<div style="margin-bottom:1.25rem;">
  <div style="font-size:.85rem;font-weight:600;margin-bottom:.6rem;color:var(--text);">
    Update Contact Information
  </div>
  <form method="POST" action="{{ route('account.contact') }}">
    @csrf
    <div class="form-group">
      <label class="form-label">Full Name</label>
      <input type="text" name="name" class="form-control @error('name') is-error @enderror"
             value="{{ old('name', $user->name) }}" required maxlength="255">
      @error('name')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label class="form-label">Contact Number</label>
      <input type="tel" name="phone" class="form-control @error('phone') is-error @enderror"
             value="{{ old('phone', $user->phone) }}" required maxlength="20">
      @error('phone')<div class="form-error">{{ $message }}</div>@enderror
    </div>
    <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;">
      Update Contact Info
    </button>
  </form>
</div>

{{-- ── PAYMENT SUBMITTED STATE ── --}}
@else

<div style="text-align:center;">
  <div style="font-size:3rem;margin-bottom:1rem;">&#9203;</div>
  <h2 class="auth-title">Payment Under Review</h2>
  <p class="auth-subtitle" style="margin-bottom:1.5rem;">Your account registration is almost complete</p>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:1rem;">{{ session('success') }}</div>
@endif
@if(session('contact_updated'))
  <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; Contact information updated.</div>
@endif

<div class="card" style="margin-bottom:1.25rem;">
  <div style="font-size:.9rem;line-height:1.75;color:var(--text);">
    After payment verification, your account will be approved. Our team will contact you on your
    registered number within a few hours.
  </div>
  @if($user->phone)
  <div style="margin-top:.75rem;padding:.55rem .85rem;background:var(--bg);border-radius:6px;font-size:.875rem;">
    <span class="text-muted">We will call you on:</span>
    <strong style="margin-left:.4rem;">{{ $user->phone }}</strong>
  </div>
  @endif
</div>

<div style="display:grid;gap:.45rem;font-size:.85rem;margin-bottom:1.5rem;">
  <div style="display:flex;align-items:center;gap:.6rem;color:var(--success);">
    <span>&#10003;</span> Account information submitted
  </div>
  <div style="display:flex;align-items:center;gap:.6rem;color:var(--success);">
    <span>&#10003;</span> Payment screenshot uploaded
  </div>
  <div style="display:flex;align-items:center;gap:.6rem;color:var(--warning);">
    <span>&#9679;</span> Awaiting payment verification
  </div>
  <div style="display:flex;align-items:center;gap:.6rem;color:var(--muted);">
    <span>&#9675;</span> Account activation
  </div>
</div>

<a href="{{ route('dashboard') }}" class="btn btn-primary"
   style="width:100%;justify-content:center;padding:.6rem;margin-bottom:1rem;">
  Check Account Status
</a>

{{-- Update contact info --}}
<details style="margin-bottom:1rem;">
  <summary style="cursor:pointer;font-size:.85rem;font-weight:600;color:var(--primary);padding:.4rem 0;list-style:none;display:flex;align-items:center;gap:.4rem;">
    <span>&#9998;</span> Wrong contact number? Update here
  </summary>
  <div style="margin-top:.75rem;">
    <form method="POST" action="{{ route('account.contact') }}">
      @csrf
      <div class="form-group">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control @error('name') is-error @enderror"
               value="{{ old('name', $user->name) }}" required maxlength="255">
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">Contact Number</label>
        <input type="tel" name="phone" class="form-control @error('phone') is-error @enderror"
               value="{{ old('phone', $user->phone) }}" required maxlength="20">
        @error('phone')<div class="form-error">{{ $message }}</div>@enderror
      </div>
      <button type="submit" class="btn btn-secondary" style="width:100%;justify-content:center;">
        Save Changes
      </button>
    </form>
  </div>
</details>

@endif

{{-- Logout always visible --}}
<form method="POST" action="{{ route('logout') }}">
  @csrf
  <button type="submit" class="btn btn-secondary"
          style="width:100%;justify-content:center;padding:.5rem;font-size:.85rem;">
    Sign Out
  </button>
</form>

@endsection
