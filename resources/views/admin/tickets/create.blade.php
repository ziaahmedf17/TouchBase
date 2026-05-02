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
      <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror"
             value="{{ old('subject') }}" placeholder="Brief description of the issue" required>
      @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Description <span style="color:var(--danger)">*</span></label>
      <textarea name="description" rows="6"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Describe the issue in detail..." required>{{ old('description') }}</textarea>
      @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Submit Ticket</button>
      <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
