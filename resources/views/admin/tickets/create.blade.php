@extends('layouts.app')
@section('title', 'Raise a Ticket')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Raise a Support Ticket</h1>
  <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:620px;">
  <form method="POST" action="{{ route('admin.tickets.store') }}">
    @csrf

    <div class="form-group">
      <label class="form-label">Subject <span style="color:var(--danger)">*</span></label>
      <input type="text" name="subject" id="subject"
             class="form-control @error('subject') is-invalid @enderror"
             value="{{ old('subject') }}"
             placeholder="Brief description of the issue"
             maxlength="100" required>
      <div style="display:flex;justify-content:space-between;margin-top:.25rem;">
        @error('subject')
          <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
        @else
          <small class="text-muted">Keep it short and clear</small>
        @enderror
        <small id="subject-count" class="text-muted">0 / 100</small>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Description <span style="color:var(--danger)">*</span></label>
      <textarea name="description" id="description" rows="6"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Describe the issue in detail. Include steps to reproduce if applicable."
                maxlength="2000" required>{{ old('description') }}</textarea>
      <div style="display:flex;justify-content:space-between;margin-top:.25rem;">
        @error('description')
          <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
        @else
          <small class="text-muted">Max 2000 characters</small>
        @enderror
        <small id="desc-count" class="text-muted">0 / 2000</small>
      </div>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Submit Ticket</button>
      <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  function charCount(inputId, countId, max) {
    const el = document.getElementById(inputId);
    const counter = document.getElementById(countId);
    if (!el || !counter) return;
    const update = () => {
      const len = el.value.length;
      counter.textContent = len + ' / ' + max;
      counter.style.color = len > max * 0.9 ? 'var(--danger)' : '';
    };
    el.addEventListener('input', update);
    update();
  }
  charCount('subject', 'subject-count', 100);
  charCount('description', 'desc-count', 2000);
</script>
@endpush
