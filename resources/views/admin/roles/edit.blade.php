@extends('layouts.app')
@section('title', 'Edit Role')

@section('content')
<div class="page-header">
  <h1 class="page-title">Edit Role: {{ $role->name }}</h1>
  <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:700px;">
  <form method="POST" action="{{ route('admin.roles.update', $role) }}">
    @csrf @method('PUT')

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name', $role->name) }}" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Slug <span style="color:var(--danger)">*</span></label>
      <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
             value="{{ old('slug', $role->slug) }}"
             {{ $role->slug === 'admin' ? 'readonly' : '' }} required>
      <small class="text-muted">Lowercase letters, numbers, and hyphens only.</small>
      @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-control"
             value="{{ old('description', $role->description) }}">
    </div>

    <div class="form-group">
      <label class="form-label">Permissions</label>
      @foreach($permissions as $group => $perms)
        <div style="margin-bottom:1rem;">
          <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:.4rem;">
            <div style="font-weight:600;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);">
              {{ $group }}
            </div>
            <button type="button" class="btn btn-sm btn-secondary" style="padding:.1rem .5rem;font-size:.75rem;"
                    onclick="toggleGroup(this)">Select all</button>
          </div>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.4rem;"
               class="perm-group">
            @foreach($perms as $perm)
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem;">
              <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                     {{ in_array($perm->id, old('permissions', $assigned)) ? 'checked' : '' }}>
              <span>{{ $perm->name }}</span>
              @if($perm->description)
                <span class="text-muted" style="font-size:.78rem;" title="{{ $perm->description }}">&#9432;</span>
              @endif
            </label>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">Save Changes</button>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  function toggleGroup(btn) {
    const group = btn.closest('div').nextElementSibling;
    const boxes = group.querySelectorAll('input[type=checkbox]');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => b.checked = !allChecked);
    btn.textContent = allChecked ? 'Select all' : 'Deselect all';
  }
</script>
@endpush
