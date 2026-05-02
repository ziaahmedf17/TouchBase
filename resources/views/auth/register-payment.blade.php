@extends('layouts.auth')
@section('title', 'Payment')

@section('content')
<div style="text-align:center;margin-bottom:1.25rem;">
  <div style="display:flex;justify-content:center;gap:0;margin-bottom:.5rem;">
    <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;color:var(--muted);">
      <span style="width:22px;height:22px;border-radius:50%;background:var(--success);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;">&#10003;</span>
      Account Info
    </div>
    <div style="width:40px;height:2px;background:var(--primary);margin:0 .5rem;align-self:center;"></div>
    <div style="display:flex;align-items:center;gap:.4rem;font-size:.82rem;font-weight:600;color:var(--primary);">
      <span style="width:22px;height:22px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;">2</span>
      Payment
    </div>
  </div>
</div>

<h2 class="auth-title">Complete Payment</h2>
<p class="auth-subtitle">Transfer the subscription fee and upload your payment screenshot</p>

@if($accounts->isEmpty())
  <div class="alert alert-info">
    Payment account details are being set up. Please contact support to complete your registration.
  </div>
@else

{{-- Payment account cards --}}
<div style="display:grid;gap:.75rem;margin-bottom:1.5rem;">
  @foreach($accounts as $account)
  <div class="card" style="padding:1rem 1.1rem;">
    <div style="font-weight:700;font-size:.95rem;margin-bottom:.6rem;color:var(--text);">
      {{ $account->title }}
    </div>
    <table style="width:100%;font-size:.875rem;border-collapse:collapse;">
      <tr>
        <td style="color:var(--muted);padding:.25rem 0;width:42%;vertical-align:top;">Bank</td>
        <td style="font-weight:500;padding:.25rem 0;">{{ $account->bank_name }}</td>
      </tr>
      <tr>
        <td style="color:var(--muted);padding:.25rem 0;vertical-align:top;">Account Title</td>
        <td style="font-weight:500;padding:.25rem 0;">{{ $account->account_holder }}</td>
      </tr>
      <tr>
        <td style="color:var(--muted);padding:.25rem 0;vertical-align:top;">Account No.</td>
        <td style="padding:.25rem 0;">
          <span style="font-weight:600;font-family:monospace;letter-spacing:.03em;">{{ $account->account_number }}</span>
          <button type="button" class="btn btn-sm btn-secondary" style="margin-left:.5rem;padding:.15rem .5rem;font-size:.72rem;"
                  data-copy="{{ $account->account_number }}">Copy</button>
        </td>
      </tr>
      @if($account->iban)
      <tr>
        <td style="color:var(--muted);padding:.25rem 0;vertical-align:top;">IBAN</td>
        <td style="padding:.25rem 0;">
          <span style="font-weight:600;font-family:monospace;font-size:.82rem;letter-spacing:.03em;">{{ $account->iban }}</span>
          <button type="button" class="btn btn-sm btn-secondary" style="margin-left:.5rem;padding:.15rem .5rem;font-size:.72rem;"
                  data-copy="{{ $account->iban }}">Copy</button>
        </td>
      </tr>
      @endif
    </table>
    @if($account->instructions)
      <div style="margin-top:.6rem;padding:.5rem .75rem;background:var(--bg);border-radius:6px;font-size:.8rem;color:var(--muted);line-height:1.5;">
        {{ $account->instructions }}
      </div>
    @endif
  </div>
  @endforeach
</div>

{{-- Upload screenshot --}}
<form method="POST" action="{{ route('register.payment.store') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group">
    <label class="form-label" for="screenshot">
      Upload payment screenshot
      <span class="text-muted" style="font-weight:400;font-size:.82rem;">(JPG, PNG, or PDF — max 5 MB)</span>
    </label>
    <input type="file" class="form-control @error('screenshot') is-error @enderror"
           id="screenshot" name="screenshot"
           accept=".jpg,.jpeg,.png,.gif,.pdf" required>
    @error('screenshot')<div class="form-error">{{ $message }}</div>@enderror
  </div>

  <div id="preview-wrap" style="display:none;margin-bottom:1rem;">
    <img id="preview-img" src="" alt="Preview" style="max-width:100%;border-radius:var(--radius);border:1px solid var(--border);">
    <div id="preview-name" class="text-muted" style="font-size:.8rem;margin-top:.3rem;"></div>
  </div>

  <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.6rem;">
    Submit Payment Screenshot
  </button>
</form>

@endif

<p style="text-align:center;margin-top:1.25rem;font-size:.82rem;color:var(--muted);">
  Wrong account? <a href="{{ route('register') }}">Go back</a>
</p>
@endsection

@push('scripts')
<script>
  // Preview image before upload
  document.getElementById('screenshot').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    document.getElementById('preview-name').textContent = file.name;
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-wrap').style.display = 'block';
      };
      reader.readAsDataURL(file);
    } else {
      document.getElementById('preview-img').style.display = 'none';
      document.getElementById('preview-wrap').style.display = 'block';
    }
  });
</script>
@endpush
