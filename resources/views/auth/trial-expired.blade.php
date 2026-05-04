@extends('layouts.auth')
@section('title', 'Trial Expired')

@section('content')
<div style="text-align:center;margin-bottom:1.25rem;">
  <div style="display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:50%;background:#dbeafe;margin-bottom:.75rem;">
    <span style="font-size:1.4rem;">&#127775;</span>
  </div>
  <h2 class="auth-title" style="margin-bottom:.3rem;">Your Free Trial Has Ended</h2>
  <p class="auth-subtitle">Subscribe to a paid plan to continue using TouchBase and keep all your data.</p>
</div>

@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="alert" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;margin-bottom:1.25rem;font-size:.88rem;line-height:1.6;">
  Your trial data is safe. Subscribe now and pick up right where you left off. All your clients, events, and interactions are still here.
</div>

@if($accounts->isNotEmpty())
<div style="display:grid;gap:.75rem;margin-bottom:1.5rem;">
  @foreach($accounts as $account)
  <div class="card" style="padding:1rem 1.1rem;">
    <div style="font-weight:700;font-size:.95rem;margin-bottom:.6rem;color:var(--text);">{{ $account->title }}</div>
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
@else
  <div class="alert alert-info" style="margin-bottom:1.5rem;">
    Payment account details are being set up. Please contact support to get started.
  </div>
@endif

<form method="POST" action="{{ route('account.trial_upgrade') }}" enctype="multipart/form-data">
  @csrf

  <div class="form-group" style="margin-bottom:1.25rem;">
    <label class="form-label">Select a Subscription Plan</label>
    @error('plan_type')<div class="form-error" style="margin-bottom:.5rem;">{{ $message }}</div>@enderror
    <div style="display:grid;gap:.6rem;">
      @foreach(['monthly','yearly','lifetime'] as $slug)
        @php $p = $plans[$slug] ?? null; @endphp
        @if($p)
        <label style="display:flex;align-items:center;gap:.75rem;padding:.75rem 1rem;border:2px solid var(--border);border-radius:var(--radius);cursor:pointer;transition:border-color .15s;"
               onclick="this.style.borderColor='var(--primary)'">
          <input type="radio" name="plan_type" value="{{ $p->slug }}"
                 {{ old('plan_type') === $p->slug ? 'checked' : '' }}
                 style="accent-color:var(--primary);width:16px;height:16px;flex-shrink:0;"
                 required>
          <div style="flex:1;">
            <div style="font-weight:700;font-size:.95rem;">{{ $p->name }}</div>
            <div class="text-muted" style="font-size:.8rem;">
              @if($p->slug === 'lifetime') Lifetime access, pay once
              @elseif($p->slug === 'yearly') Valid for 1 year
              @else Valid for 30 days
              @endif
            </div>
          </div>
          <div style="font-weight:700;font-size:1rem;color:var(--primary);white-space:nowrap;">{{ $p->formattedPrice() }}</div>
        </label>
        @endif
      @endforeach
    </div>
  </div>

  <div class="form-group">
    <label class="form-label" for="screenshot">
      Upload payment screenshot
      <span class="text-muted" style="font-weight:400;font-size:.82rem;">(JPG, PNG, or PDF, max 5 MB)</span>
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
    Submit Payment &amp; Activate
  </button>
</form>

<div style="text-align:center;margin-top:1.5rem;">
  <form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit" class="btn btn-secondary" style="font-size:.85rem;">Sign Out</button>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('screenshot')?.addEventListener('change', function () {
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
