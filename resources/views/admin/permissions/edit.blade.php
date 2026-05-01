@extends('layouts.app')
@section('title', 'Edit Permission')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">Edit Permission</h1>
  <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:560px;">
  <form method="POST" action="{{ route('admin.permissions.update', $permission) }}">
    @csrf @method('PUT')

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $permission->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Slug <span style="color:var(--danger)">*</span></label>
      <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
             value="{{ old('slug', $permission->slug) }}" required>
      <small class="text-muted">Lowercase letters, numbers, dots, and hyphens only.</small>
      @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Group <span style="color:var(--danger)">*</span></label>
      <input type="text" name="group" list="group-list"
             class="form-control @error('group') is-invalid @enderror"
             value="{{ old('group', $permission->group) }}" required>
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
             value="{{ old('description', $permission->description) }}">
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection
