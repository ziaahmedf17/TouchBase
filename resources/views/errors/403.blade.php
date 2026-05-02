@extends('layouts.app')
@section('title', 'Access Denied')

@section('content')
<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:4rem 1rem;text-align:center;">
  <div style="font-size:3rem;margin-bottom:1rem;">&#128274;</div>
  <h1 style="font-size:1.5rem;font-weight:700;margin-bottom:.5rem;">Access Denied</h1>
  <p class="text-muted" style="max-width:380px;margin-bottom:1.5rem;">
    You don't have permission to access this page. Contact your admin if you think this is a mistake.
  </p>
  <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}" class="btn btn-secondary">&#8592; Go Back</a>
</div>
@endsection
