@extends('layouts.app')
@section('title', 'Edit Event')

@section('content')
<div class="page-header">
  <h1 class="page-title">Edit Event — {{ $client->name }}</h1>
  <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">&larr; Back</a>
</div>

<div class="card" style="max-width:580px;">
  <form method="POST" action="{{ route('clients.events.update', [$client, $event]) }}">
    @csrf @method('PUT')

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Event Type</label>
        <select class="form-control" name="type" id="event-type" required>
          <option value="birthday"    {{ old('type', $event->type) === 'birthday'    ? 'selected' : '' }}>Birthday</option>
          <option value="anniversary" {{ old('type', $event->type) === 'anniversary' ? 'selected' : '' }}>Anniversary</option>
          <option value="visit"       {{ old('type', $event->type) === 'visit'       ? 'selected' : '' }}>Visit</option>
          <option value="custom"      {{ old('type', $event->type) === 'custom'      ? 'selected' : '' }}>Custom</option>
        </select>
      </div>
      <div class="form-group" id="label-group">
        <label class="form-label">Custom Label</label>
        <input class="form-control" type="text" name="label"
               value="{{ old('label', $event->label) }}" placeholder="e.g. Work Anniversary">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Date</label>
        <input class="form-control" type="date" name="event_date"
               value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" required>
      </div>
      <div class="form-group">
        <label class="form-label">Remind (days before)</label>
        <input class="form-control" type="text" name="reminder_days"
               value="{{ old('reminder_days', implode(', ', $event->reminder_days ?? [])) }}"
               placeholder="e.g. 1, 3, 7">
      </div>
    </div>

    <div class="form-group" id="annual-group">
      <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;">
        <input type="hidden" name="is_annual" value="0">
        <input type="checkbox" name="is_annual" value="1"
               {{ old('is_annual', $event->is_annual) ? 'checked' : '' }}>
        <span>Repeats every year (annual event)</span>
      </label>
    </div>

    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn btn-primary">Update Event</button>
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
