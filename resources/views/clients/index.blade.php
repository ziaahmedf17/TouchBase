@extends('layouts.app')
@section('title', 'Clients')

@section('content')
<div class="page-header">
  <h1 class="page-title">Clients</h1>
  @can('clients.create')<a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add Client</a>@endcan
</div>

{{-- Search --}}
<form method="GET" action="{{ route('clients.index') }}" class="search-bar">
  <input class="form-control" type="text" name="search" placeholder="Search by name or phone…" value="{{ $search ?? '' }}">
  <button class="btn btn-secondary" type="submit">Search</button>
  @if($search)
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Clear</a>
  @endif
</form>

@if($clients->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128101;</div>
    <p>No clients found. @can('clients.create')<a href="{{ route('clients.create') }}">Add your first client</a>.@endcan</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Phone</th>
          <th>Next Visit</th>
          <th>Events</th>
          <th style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($clients as $client)
        <tr>
          <td>
            <a href="{{ route('clients.show', $client) }}" style="font-weight:600;">
              {{ $client->name }}
            </a>
            @if($client->notifications_count > 0)
              <span class="badge badge-custom">{{ $client->notifications_count }} alerts</span>
            @endif
          </td>
          <td>
            @if($client->phone)
              <a href="{{ $client->telUrl() }}">{{ $client->phone }}</a>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>
            @if($client->next_visit_date)
              {{ $client->next_visit_date->format('d M Y') }}
              @if($client->next_visit_date->isPast())
                <span class="badge badge-custom">Past</span>
              @elseif($client->next_visit_date->diffInDays(now()) <= 7)
                <span class="badge badge-visit">Soon</span>
              @endif
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td>{{ $client->events_count }}</td>
          <td>
            <div class="d-flex gap-2">
              <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-secondary">View</a>
              @can('clients.edit')
              <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-primary">Edit</a>
              @endcan
              @can('clients.delete')
              <form method="POST" action="{{ route('clients.destroy', $client) }}"
                    data-confirm="Delete {{ $client->name }}? All their events and alerts will also be removed.">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Del</button>
              </form>
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Pagination --}}
<div>{{ $clients->links('partials.pagination') }}</div>
@endif
@endsection
