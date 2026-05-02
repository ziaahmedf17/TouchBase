@extends('layouts.app')
@section('title', $admin->name . ' — Payment')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $admin->name }}</h1>
    <div class="text-muted" style="font-size:.85rem;">Payment verification</div>
  </div>
  <a href="{{ route('superadmin.payments.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="grid-sidebar">

  {{-- Left: payment screenshot + admin info --}}
  <div style="display:grid;gap:1rem;">

    {{-- Current status --}}
    <div style="padding:.75rem 1.25rem;border-radius:var(--radius);font-weight:600;font-size:.9rem;{{ $admin->accountStatusBadgeStyle() }}">
      Status: {{ $admin->accountStatusLabel() }}
    </div>

    {{-- Screenshot --}}
    <div class="card">
      <div class="card-title">Payment Screenshot</div>
      @if($admin->payment_screenshot)
        @php $ext = strtolower(pathinfo($admin->payment_screenshot, PATHINFO_EXTENSION)); @endphp
        @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
          <img src="{{ route('superadmin.payments.screenshot', $admin->payment_screenshot) }}"
               alt="Payment screenshot"
               style="max-width:100%;border-radius:var(--radius);border:1px solid var(--border);">
        @else
          <a href="{{ route('superadmin.payments.screenshot', $admin->payment_screenshot) }}"
             target="_blank" class="btn btn-secondary">
            &#128196; View PDF Screenshot
          </a>
        @endif
        @if($admin->payment_submitted_at)
          <div class="text-muted" style="font-size:.78rem;margin-top:.6rem;">
            Submitted {{ $admin->payment_submitted_at->format('d M Y \a\t H:i') }}
          </div>
        @endif
      @else
        <p class="text-muted" style="margin:0;">No screenshot uploaded.</p>
      @endif
    </div>

    {{-- Admin info --}}
    <div class="card">
      <div class="card-title">Admin Information</div>
      <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);width:40%;">Email</td>
          <td style="padding:.4rem 0;font-weight:500;">{{ $admin->email }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Phone</td>
          <td style="padding:.4rem 0;font-weight:500;">
            @if($admin->phone)
              <a href="tel:{{ $admin->phone }}">{{ $admin->phone }}</a>
            @else
              —
            @endif
          </td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Business Type</td>
          <td style="padding:.4rem 0;">{{ $admin->business_type ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);vertical-align:top;">Description</td>
          <td style="padding:.4rem 0;line-height:1.6;">{{ $admin->business_description ?? '—' }}</td>
        </tr>
        <tr>
          <td style="padding:.4rem 0;color:var(--muted);">Registered</td>
          <td style="padding:.4rem 0;">{{ $admin->created_at->format('d M Y, H:i') }}</td>
        </tr>
      </table>
    </div>

  </div>

  {{-- Right: action panel --}}
  <div class="card sticky-panel">
    <div class="card-title">Account Decision</div>

    @if($admin->account_status === 'active')
      <div class="alert alert-success" style="margin-bottom:1rem;">
        &#10003; This account is already approved and active.
      </div>
    @endif

    @if($admin->account_status !== 'active')
    <form method="POST" action="{{ route('superadmin.payments.approve', $admin) }}">
      @csrf
      <div class="form-group" style="margin-bottom:.75rem;">
        <label class="form-label" style="font-size:.82rem;">Confirm Plan</label>
        <select name="plan_type" class="form-control" style="font-size:.85rem;" required>
          @foreach(['monthly','yearly','lifetime'] as $slug)
            @php $p = $plans[$slug] ?? null; @endphp
            @if($p)
              <option value="{{ $p->slug }}" {{ ($admin->plan_type ?? 'monthly') === $p->slug ? 'selected' : '' }}>
                {{ $p->name }} — {{ $p->formattedPrice() }}
              </option>
            @endif
          @endforeach
        </select>
      </div>
      <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;margin-bottom:.6rem;"
              onclick="return confirm('Approve account for {{ addslashes($admin->name) }}? Remember to call them on {{ $admin->phone }}.')">
        &#10003; Approve &amp; Activate
      </button>
    </form>
    @endif

    @if($admin->account_status === 'payment_submitted')
    <form method="POST" action="{{ route('superadmin.payments.reject', $admin) }}"
          data-confirm="Reject payment for &quot;{{ $admin->name }}&quot;? They will need to resubmit.">
      @csrf
      <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center;">
        &#10007; Reject Payment
      </button>
    </form>
    @endif

    @if($admin->phone)
    <div style="margin-top:1rem;padding:.75rem;background:var(--bg);border-radius:var(--radius);font-size:.85rem;line-height:1.6;">
      <div style="font-weight:600;margin-bottom:.25rem;">&#128222; After approval, call:</div>
      <a href="tel:{{ $admin->phone }}" style="font-size:1rem;font-weight:700;">{{ $admin->phone }}</a>
      <div class="text-muted" style="font-size:.78rem;margin-top:.25rem;">
        Inform them their account is active and they can log in.
      </div>
    </div>
    @endif
  </div>

</div>
@endsection
