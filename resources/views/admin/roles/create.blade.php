@extends('layouts.app')
@section('title', 'New Role')

@section('content')
@include('partials.admin_nav')
<div class="page-header">
  <h1 class="page-title">New Role</h1>
  <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

<div class="card" style="max-width:700px;">
  <form method="POST" action="{{ route('admin.roles.store') }}">
    @csrf

    <div class="form-group">
      <label class="form-label">Name <span style="color:var(--danger)">*</span></label>
      <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
             value="{{ old('name') }}" placeholder="e.g. Manager" required>
      @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Slug <span style="color:var(--danger)">*</span></label>
      <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror"
             value="{{ old('slug') }}" placeholder="e.g. manager" required>
      <small class="text-muted">Lowercase letters, numbers, and hyphens only.</small>
      @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group">
      <label class="form-label">Description</label>
      <input type="text" name="description" class="form-control"
             value="{{ old('description') }}" placeholder="Optional description">
    </div>

    <div class="form-group">
      <label class="form-label">Permissions</label>
      @foreach($permissions as $group => $perms)
        <div style="margin-bottom:1rem;">
          <div style="font-weight:600;font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;color:var(--muted);margin-bottom:.4rem;">
            {{ $group }}
          </div>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.4rem;">
            @foreach($perms as $perm)
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.9rem;">
              <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                     {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
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
      <button type="submit" class="btn btn-primary">Create Role</button>
      <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
  document.querySelector('[name="name"]').addEventListener('input', function () {
    const slug = document.getElementById('slug');
    if (!slug.dataset.edited) {
      slug.value = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
  });
  document.getElementById('slug').addEventListener('input', function () {
    this.dataset.edited = '1';
  });
</script>
@endpush
