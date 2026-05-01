@extends('layouts.app')
@section('title', 'Add Client')

@section('content')
<div class="page-header">
  <h1 class="page-title">Add Client</h1>
  <a href="{{ route('clients.index') }}" class="btn btn-secondary">&larr; Back</a>
</div>

<div class="card" style="max-width:640px;">
  <form method="POST" action="{{ route('clients.store') }}">
    @csrf

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required autofocus>
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input class="form-control" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+92300…">
        @error('phone')<div class="form-error">{{ $message }}</div>@enderror
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Address</label>
      <input class="form-control" type="text" name="address" value="{{ old('address') }}">
    </div>

    <div class="form-group">
      <label class="form-label">Notes</label>
      <textarea class="form-control" name="notes" rows="3">{{ old('notes') }}</textarea>
    </div>

    <hr style="margin:1rem 0;border:none;border-top:1px solid var(--border);">

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Next Visit Date</label>
        <input class="form-control" type="date" name="next_visit_date" value="{{ old('next_visit_date') }}">
      </div>
      <div class="form-group">
        <label class="form-label">Remind me (days before)</label>
        <input class="form-control" type="text" name="visit_reminder_days"
               value="{{ old('visit_reminder_days') }}" placeholder="e.g. 1, 3, 7">
        <div class="form-hint">Comma or space separated numbers</div>
      </div>
    </div>

    <hr style="margin:1.5rem 0;border:none;border-top:1px solid var(--border);">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <h3 style="font-size:1rem;font-weight:600;color:var(--text);margin:0;">Life Events</h3>
      <button type="button" class="btn btn-secondary" id="add-event-btn" style="font-size:.85rem;padding:.3rem .75rem;">+ Add Event</button>
    </div>

    <div id="events-list"></div>

    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn btn-primary">Save Client</button>
      <a href="{{ route('clients.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
(function () {
  let eventIndex = 0;

  const template = () => {
    const i = eventIndex++;
    return `
      <div class="event-row" style="border:1px solid var(--border);border-radius:var(--radius);padding:1rem;margin-bottom:.75rem;position:relative;">
        <button type="button" class="remove-event-btn" title="Remove"
          style="position:absolute;top:.5rem;right:.5rem;background:none;border:none;cursor:pointer;color:var(--danger);font-size:1.1rem;line-height:1;">&times;</button>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Event Type <span style="color:var(--danger)">*</span></label>
            <select class="form-control event-type-select" name="events[${i}][type]" required>
              <option value="">Select…</option>
              <option value="birthday">Birthday</option>
              <option value="anniversary">Anniversary</option>
              <option value="visit">Visit</option>
              <option value="custom">Custom</option>
            </select>
          </div>
          <div class="form-group event-label-group" style="display:none;">
            <label class="form-label">Custom Label</label>
            <input class="form-control" type="text" name="events[${i}][label]" placeholder="e.g. Work Anniversary">
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Date <span style="color:var(--danger)">*</span></label>
            <input class="form-control" type="date" name="events[${i}][event_date]" required>
          </div>
          <div class="form-group">
            <label class="form-label">Remind (days before)</label>
            <input class="form-control" type="text" name="events[${i}][reminder_days]" placeholder="e.g. 1, 3, 7">
          </div>
        </div>

        <div class="form-group event-recurrence-group">
          <label class="form-label">Recurrence</label>
          <select class="form-control event-recurrence-select" name="events[${i}][recurrence]">
            <option value="none">No repeat (one-time)</option>
            <option value="weekly">Weekly</option>
            <option value="biweekly">Bi-weekly (every 2 weeks)</option>
            <option value="monthly">Monthly</option>
            <option value="annual">Annual (every year)</option>
          </select>
          <div class="form-hint recurrence-note" style="display:none;">Birthday &amp; Anniversary always repeat annually.</div>
        </div>
      </div>`;
  };

  function bindRow(row) {
    const typeSelect       = row.querySelector('.event-type-select');
    const labelGroup       = row.querySelector('.event-label-group');
    const recurrenceSelect = row.querySelector('.event-recurrence-select');
    const recurrenceNote   = row.querySelector('.recurrence-note');

    typeSelect.addEventListener('change', function () {
      const t = this.value;
      labelGroup.style.display = t === 'custom' ? '' : 'none';
      if (t === 'birthday' || t === 'anniversary') {
        recurrenceSelect.value    = 'annual';
        recurrenceSelect.disabled = true;
        recurrenceNote.style.display = '';
      } else {
        recurrenceSelect.disabled = false;
        recurrenceNote.style.display = 'none';
      }
    });

    row.querySelector('.remove-event-btn').addEventListener('click', function () {
      row.remove();
    });
  }

  document.getElementById('add-event-btn').addEventListener('click', function () {
    const list = document.getElementById('events-list');
    const tmp  = document.createElement('div');
    tmp.innerHTML = template().trim();
    const row = tmp.firstChild;
    list.appendChild(row);
    bindRow(row);
  });
})();
</script>
@endpush
@endsection
