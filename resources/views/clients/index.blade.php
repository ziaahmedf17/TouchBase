@extends('layouts.app')
@section('title', 'Clients')

@section('content')
<div class="page-header">
  <h1 class="page-title">Clients</h1>
  @can('clients.create')<a href="{{ route('clients.create') }}" class="btn btn-primary">+ Add Client</a>@endcan
</div>

{{-- Search + filters --}}
<form method="GET" action="{{ route('clients.index') }}" class="search-bar">
  <input class="form-control" type="text" name="search" placeholder="Search by name or phone…" value="{{ $search ?? '' }}">
  @if($visit ?? null)
    <input type="hidden" name="visit" value="{{ $visit }}">
  @endif
  <button class="btn btn-secondary" type="submit">Search</button>
  @if(($search ?? '') || ($visit ?? ''))
    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Clear</a>
  @endif
</form>

{{-- Visit filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:1rem;">
  <a href="{{ route('clients.index', array_filter(['search' => $search])) }}"
     class="btn btn-sm {{ !($visit ?? null) ? 'btn-primary' : 'btn-secondary' }}">All</a>
  <a href="{{ route('clients.index', array_filter(['search' => $search, 'visit' => 'week'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'week' ? 'btn-primary' : 'btn-secondary' }}">This Week</a>
  <a href="{{ route('clients.index', array_filter(['search' => $search, 'visit' => 'month'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'month' ? 'btn-primary' : 'btn-secondary' }}">This Month</a>
  <a href="{{ route('clients.index', array_filter(['search' => $search, 'visit' => 'overdue'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'overdue' ? 'btn-danger' : 'btn-secondary' }}">Overdue</a>
</div>

@if($clients->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128101;</div>
    <p>No clients found. @can('clients.create')<a href="{{ route('clients.create') }}">Add your first client</a>.@endcan</p>
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
          <th style="width:130px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($clients as $client)
        <tr>
          <td data-label="Name">
            <a href="{{ route('clients.show', $client) }}" style="font-weight:600;">
              {{ $client->name }}
            </a>
            @if($client->notifications_count > 0)
              <span class="badge badge-custom">{{ $client->notifications_count }} alerts</span>
            @endif
          </td>
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
              @if($client->next_visit_date->isPast())
                <span class="badge badge-custom">Past</span>
              @elseif($client->next_visit_date->diffInDays(now()) <= 7)
                <span class="badge badge-visit">Soon</span>
              @endif
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Events">{{ $client->events_count }}</td>
          <td data-label="Actions">
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
