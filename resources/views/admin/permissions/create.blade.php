@extends('layouts.app')
@section('title', 'New Permission')

@section('content')
<div class="page-header">
  <h1 class="page-title">New Permission</h1>
  <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('admin.permissions.store') }}">
    @csrf

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}" placeholder="e.g. View Clients" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Slug <span style="color:var(--danger)">*</span></label>
      <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
             value="{{ old('slug') }}" placeholder="e.g. clients.view" required>
      <small class="text-muted">Lowercase letters, numbers, dots, and hyphens only.</small>
      @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Group <span style="color:var(--danger)">*</span></label>
      <input type="text" name="group" list="group-list"
             class="form-control @error('group') is-invalid @enderror"
             value="{{ old('group') }}" placeholder="e.g. clients" required>
      <datalist id="group-list">
        @foreach($groups as $g)
          <option value="{{ $g }}">
        @endforeach
      </datalist>
      @error('group')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-control"
             value="{{ old('description') }}" placeholder="Optional description">
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Create Permission</button>
      <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
