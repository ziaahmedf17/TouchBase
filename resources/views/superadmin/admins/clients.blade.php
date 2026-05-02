@extends('layouts.app')
@section('title', $admin->name . ' — Clients')

@section('content')
@include('partials.superadmin_nav')
<div class="page-header">
  <div>
    <h1 class="page-title">{{ $admin->name }}'s Clients</h1>
    <div class="text-muted" style="font-size:.85rem;">{{ $admin->email }}</div>
  </div>
  <a href="{{ route('superadmin.admins.index') }}" class="btn btn-secondary">&#8592; Back</a>
</div>

@if($clients->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128101;</div>
    <p>This admin has no clients yet.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
          <th>Next Visit</th>
          <th>Events</th>
          <th>Alerts</th>
        </tr>
      </thead>
      <tbody>
        @foreach($clients as $client)
        <tr>
          <td data-label="Name" style="font-weight:600;">{{ $client->name }}</td>
          <td data-label="Phone">
            @if($client->phone)
              <a href="{{ $client->telUrl() }}">{{ $client->phone }}</a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Next Visit">
            @if($client->next_visit_date)
              {{ $client->next_visit_date->format('d M Y') }}
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Events">{{ $client->events_count }}</td>
          <td data-label="Alerts">{{ $client->notifications_count }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
{{ $clients->links('partials.pagination') }}
@endif
@endsection
