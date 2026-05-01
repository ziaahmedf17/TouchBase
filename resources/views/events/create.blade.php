@extends('layouts.app')
@section('title', 'Add Event')

@section('content')
<div class="page-header">
  <h1 class="page-title">Add Event — {{ $client->name }}</h1>
  <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">&larr; Back</a>
</div>

<div class="card" style="max-width:580px;">
  <form method="POST" action="{{ route('clients.events.store', $client) }}">
    @csrf

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Event Type <span style="color:var(--danger)">*</span></label>
        <select class="form-control" name="type" id="event-type" required>
          <option value="">Select…</option>
          <option value="birthday"    {{ old('type') === 'birthday'    ? 'selected' : '' }}>Birthday</option>
          <option value="anniversary" {{ old('type') === 'anniversary' ? 'selected' : '' }}>Anniversary</option>
          <option value="visit"       {{ old('type') === 'visit'       ? 'selected' : '' }}>Visit</option>
          <option value="custom"      {{ old('type') === 'custom'      ? 'selected' : '' }}>Custom</option>
        </select>
      </div>
      <div class="form-group" id="label-group" style="{{ old('type') === 'custom' ? '' : 'display:none' }}">
        <label class="form-label">Custom Label</label>
        <input class="form-control" type="text" name="label" value="{{ old('label') }}" placeholder="e.g. Work Anniversary">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Date <span style="color:var(--danger)">*</span></label>
        <input class="form-control" type="date" name="event_date" value="{{ old('event_date') }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">Remind (days before)</label>
        <input class="form-control" type="text" name="reminder_days"
               value="{{ old('reminder_days') }}" placeholder="e.g. 1, 3, 7">
        <div class="form-hint">Comma separated. Leave empty for no reminders.</div>
      </div>
    </div>

    <div class="form-group" id="annual-group">
      <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
        <input type="hidden" name="is_annual" value="0">
        <input type="checkbox" name="is_annual" value="1" {{ old('is_annual') ? 'checked' : '' }}>
        <span>Repeats every year (annual event)</span>
      </label>
      <div class="form-hint">Birthday & Anniversary are always annual.</div>
    </div>

    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn btn-primary">Add Event</button>
      <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const typeSelect  = document.getElementById('event-type');
  const labelGroup  = document.getElementById('label-group');
  const annualGroup = document.getElementById('annual-group');
  const annualBox   = annualGroup.querySelector('input[type=checkbox]');

  function updateFields() {
    const t = typeSelect.value;
    labelGroup.style.display  = t === 'custom'  ? '' : 'none';
    // Birthday/anniversary always annual — hide the checkbox
    if (t === 'birthday' || t === 'anniversary') {
      annualGroup.style.display = 'none';
      annualBox.checked = true;
    } else {
      annualGroup.style.display = '';
    }
  }

  typeSelect.addEventListener('change', updateFields);
  updateFields();
</script>
@endpush
@endsection
