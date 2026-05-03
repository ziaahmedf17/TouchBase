@extends('layouts.app')
@section('title', 'My Profile')

@section('content')
<div class="page-header">
  <h1 class="page-title">My Profile</h1>
</div>

@if(session('success'))
  <div class="alert alert-success" data-auto-dismiss>{{ session('success') }}</div>
@endif

<div class="grid-sidebar">

  {{-- Profile form --}}
  <div>
    <div class="card">
      <div class="card-title">Account Details</div>
      <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PUT')

        <div class="form-group">
          <label class="form-label" for="name">Full Name</label>
          <input type="text" id="name" name="name"
                 class="form-control @error('name') is-error @enderror"
                 value="{{ old('name', $user->name) }}" required>
          @error('name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input type="email" id="email" name="email"
                 class="form-control @error('email') is-error @enderror"
                 value="{{ old('email', $user->email) }}" required>
          @error('email')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="phone">Phone Number</label>
          <input type="text" id="phone" name="phone"
                 class="form-control @error('phone') is-error @enderror"
                 value="{{ old('phone', $user->phone) }}">
          @error('phone')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        @if($user->isAdmin())
        <div class="form-group">
          <label class="form-label" for="business_name">Business Name</label>
          <input type="text" id="business_name" name="business_name"
                 class="form-control @error('business_name') is-error @enderror"
                 value="{{ old('business_name', $user->business_name) }}"
                 placeholder="e.g. Beauty Palace Salon">
          <div class="form-hint">Shown as signature in WhatsApp message templates.</div>
          @error('business_name')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        @endif

        <hr style="border:none;border-top:1px solid var(--border);margin:1.25rem 0;">

        <div class="card-title" style="font-size:.85rem;margin-bottom:.75rem;">Change Password <span class="text-muted" style="font-weight:400;">(leave blank to keep current)</span></div>

        <div class="form-group">
          <label class="form-label" for="password">New Password</label>
          <input type="password" id="password" name="password"
                 class="form-control @error('password') is-error @enderror"
                 autocomplete="new-password">
          @error('password')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirmation">Confirm New Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation"
                 class="form-control" autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
      </form>
    </div>
  </div>

  {{-- Side panel: subscription info (admins only) + account info --}}
  <div style="display:grid;gap:1rem;align-content:start;">

    @if($user->isAdmin())
    <div class="card sticky-panel">
      <div class="card-title">Subscription</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);width:45%;">Plan</td>
          <td style="padding:.4rem 0;font-weight:700;">{{ $user->planLabel() }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Status</td>
          <td style="padding:.4rem 0;">
            @php $alert = $user->planAlertType(); $days = $user->daysUntilExpiry(); @endphp
            @if($user->is_suspended)
              <span class="badge badge-danger">Suspended</span>
            @elseif($alert === 'grace')
              <span class="badge badge-danger">Grace Period</span>
            @elseif($alert === 'expiring')
              <span class="badge badge-warning">Expiring Soon</span>
            @elseif($user->plan_type)
              <span class="badge badge-success">Active</span>
            @else
              <span class="text-muted" style="font-size:.82rem;">No plan</span>
            @endif
          </td>
        </tr>
        @if($user->plan_started_at)
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Started</td>
          <td style="padding:.4rem 0;">{{ $user->plan_started_at->format('d M Y') }}</td>
        </tr>
        @endif
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Expires</td>
          <td style="padding:.4rem 0;font-weight:600;">
            @if($user->plan_type === 'lifetime')
              <span style="color:var(--success);">Never</span>
            @elseif($user->plan_expires_at)
              {{ $user->plan_expires_at->format('d M Y') }}
              @if($days !== null)
                <div class="{{ $days < 0 ? 'text-danger-muted' : ($days <= 14 ? 'text-warning-muted' : 'text-muted') }}" style="font-size:.75rem;margin-top:.1rem;">
                  @if($days < 0) {{ abs($days) }} days overdue
                  @else {{ $days }} days remaining
                  @endif
                </div>
              @endif
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
        </tr>
      </table>
    </div>
    @endif

    <div class="card">
      <div class="card-title">Account Info</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);width:45%;">Role</td>
          <td style="padding:.4rem 0;font-weight:500;">{{ auth()->user()->roles->first()?->name ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Member since</td>
          <td style="padding:.4rem 0;">{{ $user->created_at->format('d M Y') }}</td>
        </tr>
        @if($user->business_type)
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);vertical-align:top;">Business Type</td>
          <td style="padding:.4rem 0;">{{ $user->business_type }}</td>
        </tr>
        @endif
      </table>
    </div>

  </div>

</div>
@endsection
