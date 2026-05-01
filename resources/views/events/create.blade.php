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

    <div class="form-group" id="recurrence-group">
      <label class="form-label">Recurrence</label>
      <select class="form-control" name="recurrence" id="recurrence-select">
        <option value="none"     {{ old('recurrence', 'none') === 'none'     ? 'selected' : '' }}>No repeat (one-time)</option>
        <option value="weekly"   {{ old('recurrence') === 'weekly'           ? 'selected' : '' }}>Weekly</option>
        <option value="biweekly" {{ old('recurrence') === 'biweekly'         ? 'selected' : '' }}>Bi-weekly (every 2 weeks)</option>
        <option value="monthly"  {{ old('recurrence') === 'monthly'          ? 'selected' : '' }}>Monthly</option>
        <option value="annual"   {{ old('recurrence') === 'annual'           ? 'selected' : '' }}>Annual (every year)</option>
      </select>
      <div class="form-hint" id="recurrence-hint" style="display:none;">Birthday &amp; Anniversary always repeat annually.</div>
    </div>

    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn btn-primary">Add Event</button>
      <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  const typeSelect      = document.getElementById('event-type');
  const labelGroup      = document.getElementById('label-group');
  const recurrenceSelect = document.getElementById('recurrence-select');
  const recurrenceHint  = document.getElementById('recurrence-hint');

  function updateFields() {
    const t = typeSelect.value;
    labelGroup.style.display = t === 'custom' ? '' : 'none';
    if (t === 'birthday' || t === 'anniversary') {
      recurrenceSelect.value    = 'annual';
      recurrenceSelect.disabled = true;
      recurrenceHint.style.display = '';
    } else {
      recurrenceSelect.disabled = false;
      recurrenceHint.style.display = 'none';
    }
  }

  typeSelect.addEventListener('change', updateFields);
  updateFields();
</script>
@endpush
@endsection
