@extends('layouts.app')
@section('title', 'Edit ' . $client->name)

@section('content')
<div class="page-header">
  <h1 class="page-title">Edit Client</h1>
  <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">&larr; Back</a>
</div>

<div class="card" style="max-width:640px;">
  <form method="POST" action="{{ route('clients.update', $client) }}">
    @csrf @method('PUT')

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
        <input class="form-control" type="text" name="name"
               value="{{ old('name', $client->name) }}" required>
        @error('name')<div class="form-error">{{ $message }}</div>@enderror
      </div>
      <div class="form-group">
        <label class="form-label">Phone</label>
        <input class="form-control" type="tel" name="phone"
               value="{{ old('phone', $client->phone) }}">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Address</label>
      <input class="form-control" type="text" name="address"
             value="{{ old('address', $client->address) }}">
    </div>

    <div class="form-group">
      <label class="form-label">Notes</label>
      <textarea class="form-control" name="notes" rows="3">{{ old('notes', $client->notes) }}</textarea>
    </div>

    <hr style="margin:1rem 0;border:none;border-top:1px solid var(--border);">

    <div class="form-row">
      <div class="form-group">
        <label class="form-label">Next Visit Date</label>
        <input class="form-control" type="date" name="next_visit_date"
               value="{{ old('next_visit_date', $client->next_visit_date?->format('Y-m-d')) }}">
      </div>
      <div class="form-group">
        <label class="form-label">Remind me (days before)</label>
        <input class="form-control" type="text" name="visit_reminder_days"
               value="{{ old('visit_reminder_days', implode(', ', $client->visit_reminder_days ?? [])) }}"
               placeholder="e.g. 1, 3, 7">
        <div class="form-hint">Comma or space separated numbers</div>
      </div>
    </div>

    <div class="d-flex gap-2 mt-2">
      <button type="submit" class="btn btn-primary">Update Client</button>
      <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
