@extends('layouts.app')
@section('title', 'Add Bank Account')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <h1 class="page-title">Add Bank Account</h1>
  <a href="{{ route('superadmin.payment-accounts.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div style="max-width:560px;">
  <div class="card">
    <form method="POST" action="{{ route('superadmin.payment-accounts.store') }}">
      @csrf

      <div class="form-group">
        <label class="form-label">Account Title</label>
        <input type="text" name="title" class="form-control @error('title') is-error @enderror"
               value="{{ old('title') }}" placeholder="e.g. HBL Current Account" maxlength="100" required>
        @error('title')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Bank Name</label>
        <input type="text" name="bank_name" class="form-control @error('bank_name') is-error @enderror"
               value="{{ old('bank_name') }}" placeholder="e.g. HBL, Meezan Bank" maxlength="100" required>
        @error('bank_name')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Account Holder Name</label>
        <input type="text" name="account_holder" class="form-control @error('account_holder') is-error @enderror"
               value="{{ old('account_holder') }}" placeholder="Name as on bank account" maxlength="100" required>
        @error('account_holder')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">Account Number</label>
        <input type="text" name="account_number" class="form-control @error('account_number') is-error @enderror"
               value="{{ old('account_number') }}" placeholder="e.g. 01234567890123" maxlength="50" required>
        @error('account_number')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">
          IBAN
          <span class="text-muted" style="font-weight:400;font-size:.82rem;">(optional)</span>
        </label>
        <input type="text" name="iban" class="form-control @error('iban') is-error @enderror"
               value="{{ old('iban') }}" placeholder="e.g. PK36SCBL0000001123456702" maxlength="50">
        @error('iban')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label">
          Payment Instructions
          <span class="text-muted" style="font-weight:400;font-size:.82rem;">(optional — shown to admins during registration)</span>
        </label>
        <textarea name="instructions" class="form-control @error('instructions') is-error @enderror"
                  rows="3" maxlength="500"
                  placeholder="e.g. Please include your email in the transaction description.">{{ old('instructions') }}</textarea>
        @error('instructions')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
          <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                 style="width:16px;height:16px;">
          <span class="form-label" style="margin:0;">Show this account to registering admins</span>
        </label>
      </div>

      <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Add Account</button>
        <a href="{{ route('superadmin.payment-accounts.index') }}" class="btn btn-secondary">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
