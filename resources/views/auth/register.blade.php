@extends('layouts.auth')
@section('title', 'Create Account')

@section('content')
<h2 class="auth-title">Create account</h2>
<p class="auth-subtitle">Set up your TouchBase CRM</p>

<form method="POST" action="{{ route('register') }}">
  @csrf

  <div class="form-group">
    <label class="form-label" for="name">Full name</label>
    <input type="text" class="form-control @error('name') is-error @enderror"
           id="name" name="name" value="{{ old('name') }}"
           autocomplete="name" autofocus required>
    @error('name')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="email">Email address</label>
    <input type="email" class="form-control @error('email') is-error @enderror"
           id="email" name="email" value="{{ old('email') }}"
           autocomplete="email" required>
    @error('email')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="phone">Contact number</label>
    <input type="tel" class="form-control @error('phone') is-error @enderror"
           id="phone" name="phone" value="{{ old('phone') }}"
           placeholder="+92 300 0000000" maxlength="20" required>
    @error('phone')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="business_type">Business type</label>
    <select class="form-control @error('business_type') is-error @enderror"
            id="business_type" name="business_type" required>
      <option value="">— Select your business type —</option>
      @php
        $types = [
          'Salon & Beauty', 'Real Estate', 'Healthcare & Clinic',
          'Retail & Shop', 'Restaurant & Food', 'Fitness & Gym',
          'Education & Coaching', 'IT & Technology', 'Consulting',
          'E-commerce', 'Photography', 'Events & Wedding',
          'Travel & Tourism', 'Legal Services', 'Finance & Accounting',
        ];
      @endphp
      @foreach($types as $type)
        <option value="{{ $type }}" {{ old('business_type') === $type ? 'selected' : '' }}>
          {{ $type }}
        </option>
      @endforeach
      <option value="others" {{ old('business_type') === 'others' ? 'selected' : '' }}>
        Others
      </option>
    </select>
    @error('business_type')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group" id="custom-type-wrap" style="display:none;">
    <label class="form-label" for="custom_business_type">Please specify</label>
    <input type="text" class="form-control @error('custom_business_type') is-error @enderror"
           id="custom_business_type" name="custom_business_type"
           value="{{ old('custom_business_type') }}"
           placeholder="e.g. Pet Grooming" maxlength="100">
    @error('custom_business_type')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="business_description">
      Business description
      <span class="text-muted" style="font-weight:400;font-size:.82rem;">(max 500 characters)</span>
    </label>
    <textarea class="form-control @error('business_description') is-error @enderror"
              id="business_description" name="business_description"
              rows="3" maxlength="500"
              placeholder="Briefly describe what your business does..."
              required>{{ old('business_description') }}</textarea>
    <div style="text-align:right;margin-top:.2rem;">
      <small id="desc-count" class="text-muted">0 / 500</small>
    </div>
    @error('business_description')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="password">Password <span class="text-muted">(min. 8 characters)</span></label>
    <input type="password" class="form-control @error('password') is-error @enderror"
           id="password" name="password"
           autocomplete="new-password" required>
    @error('password')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div class="form-group">
    <label class="form-label" for="password_confirmation">Confirm password</label>
    <input type="password" class="form-control"
           id="password_confirmation" name="password_confirmation"
           autocomplete="new-password" required>
  </div>

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Create Account
  </button>
</form>

<p class="auth-switch">
  Already have an account? <a href="{{ route('login') }}">Sign in</a>
</p>
@endsection

@push('scripts')
<script>
  const typeSelect = document.getElementById('business_type');
  const customWrap = document.getElementById('custom-type-wrap');
  const customInput = document.getElementById('custom_business_type');

  function toggleCustom() {
    const show = typeSelect.value === 'others';
    customWrap.style.display = show ? 'block' : 'none';
    customInput.required = show;
  }

  typeSelect.addEventListener('change', toggleCustom);
  toggleCustom(); // run on page load (handles old() value)

  // Character counter
  const desc = document.getElementById('business_description');
  const counter = document.getElementById('desc-count');
  desc.addEventListener('input', function () {
    const len = this.value.length;
    counter.textContent = len + ' / 500';
    counter.style.color = len > 450 ? 'var(--danger)' : '';
  });
  desc.dispatchEvent(new Event('input'));
</script>
@endpush
