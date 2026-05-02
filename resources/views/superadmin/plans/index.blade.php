@extends('layouts.app')
@section('title', 'Subscription Plans')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">Subscription Plans</h1>
</div>

@if(session('success'))
  <div class="alert alert-success" data-auto-dismiss>{{ session('success') }}</div>
@endif

<div style="display:grid;gap:1rem;max-width:640px;">
  @foreach($plans as $plan)
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
      <div>
        <div style="font-weight:700;font-size:1rem;">{{ $plan->name }}</div>
        <div class="text-muted" style="font-size:.8rem;">
          {{ $plan->slug }}
          &bull;
          @if($plan->duration_days === 0) Lifetime (never expires)
          @else {{ $plan->duration_days }} days
          @endif
        </div>
      </div>
      <div style="font-size:1.3rem;font-weight:700;color:var(--primary);">{{ $plan->formattedPrice() }}</div>
    </div>

    <form method="POST" action="{{ route('superadmin.plans.update', $plan) }}">
      @csrf
      @method('PUT')
      <div style="display:flex;gap:.6rem;align-items:flex-end;">
        <div style="flex:1;">
          <label class="form-label" for="price-{{ $plan->id }}" style="font-size:.82rem;margin-bottom:.3rem;">
            New Price (Rs.)
          </label>
          <input type="number" id="price-{{ $plan->id }}" name="price"
                 class="form-control @error('price') is-error @enderror"
                 value="{{ old('price', (int)$plan->price) }}"
                 min="0" step="1" required
                 style="font-size:.9rem;">
          @error('price')<div class="form-error">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Update Price</button>
      </div>
    </form>
  </div>
  @endforeach
</div>
@endsection
