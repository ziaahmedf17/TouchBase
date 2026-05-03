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
  @if($gender ?? null)
    <input type="hidden" name="gender" value="{{ $gender }}">
  @endif
  @if(($sort ?? 'name') !== 'name' || ($dir ?? 'asc') !== 'asc')
    <input type="hidden" name="sort" value="{{ $sort }}">
    <input type="hidden" name="dir" value="{{ $dir }}">
  @endif
  <button class="btn btn-secondary" type="submit">Search</button>
  @if(($search ?? '') || ($visit ?? '') || ($gender ?? ''))
    <a href="{{ route('clients.index', array_filter(['sort' => $sort ?? null, 'dir' => $dir ?? null])) }}"
       class="btn btn-secondary">Clear</a>
  @endif
</form>

@php
  $baseParams = array_filter(['search' => $search, 'gender' => $gender, 'sort' => $sort, 'dir' => $dir]);
@endphp

{{-- Visit filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:.6rem;">
  <a href="{{ route('clients.index', array_diff_key($baseParams, ['visit' => ''])) }}"
     class="btn btn-sm {{ !($visit ?? null) ? 'btn-primary' : 'btn-secondary' }}">All</a>
  <a href="{{ route('clients.index', array_merge($baseParams, ['visit' => 'week'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'week' ? 'btn-primary' : 'btn-secondary' }}">This Week</a>
  <a href="{{ route('clients.index', array_merge($baseParams, ['visit' => 'month'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'month' ? 'btn-primary' : 'btn-secondary' }}">This Month</a>
  <a href="{{ route('clients.index', array_merge($baseParams, ['visit' => 'overdue'])) }}"
     class="btn btn-sm {{ ($visit ?? null) === 'overdue' ? 'btn-danger' : 'btn-secondary' }}">Overdue</a>
</div>

{{-- Gender filters --}}
<div class="d-flex gap-2" style="flex-wrap:wrap;margin-bottom:1rem;">
  @php $genderBase = array_filter(['search' => $search, 'visit' => $visit, 'sort' => $sort, 'dir' => $dir]); @endphp
  <a href="{{ route('clients.index', $genderBase) }}"
     class="btn btn-sm {{ !($gender ?? null) ? 'btn-primary' : 'btn-secondary' }}">All Genders</a>
  <a href="{{ route('clients.index', array_merge($genderBase, ['gender' => 'male'])) }}"
     class="btn btn-sm {{ ($gender ?? null) === 'male' ? 'btn-primary' : 'btn-secondary' }}">Male</a>
  <a href="{{ route('clients.index', array_merge($genderBase, ['gender' => 'female'])) }}"
     class="btn btn-sm {{ ($gender ?? null) === 'female' ? 'btn-primary' : 'btn-secondary' }}">Female</a>
  <a href="{{ route('clients.index', array_merge($genderBase, ['gender' => 'other'])) }}"
     class="btn btn-sm {{ ($gender ?? null) === 'other' ? 'btn-primary' : 'btn-secondary' }}">Other</a>
</div>

@if($clients->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128101;</div>
    <p>No clients found. @can('clients.create')<a href="{{ route('clients.create') }}">Add your first client</a>.@endcan</p>
  </div>
@else
@php
  $sortBase  = array_filter(['search' => $search, 'visit' => $visit, 'gender' => $gender]);
  $nameDir   = ($sort === 'name' && $dir === 'asc') ? 'desc' : 'asc';
  $visitDir  = ($sort === 'next_visit_date' && $dir === 'asc') ? 'desc' : 'asc';
@endphp
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>
            <a href="{{ route('clients.index', array_merge($sortBase, ['sort' => 'name', 'dir' => $nameDir])) }}"
               class="sort-link {{ $sort === 'name' ? 'sort-active' : '' }}">
              Name
              <span class="sort-arrow">{{ $sort === 'name' ? ($dir === 'asc' ? '↑' : '↓') : '↕' }}</span>
            </a>
          </th>
          <th>Phone</th>
          <th>
            <a href="{{ route('clients.index', array_merge($sortBase, ['sort' => 'next_visit_date', 'dir' => $visitDir])) }}"
               class="sort-link {{ $sort === 'next_visit_date' ? 'sort-active' : '' }}">
              Next Visit
              <span class="sort-arrow">{{ $sort === 'next_visit_date' ? ($dir === 'asc' ? '↑' : '↓') : '↕' }}</span>
            </a>
          </th>
          <th>Last Contact</th>
          <th style="width:170px;">Actions</th>
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
          <td data-label="Last Contact">
            @if($client->last_contacted_at)
              @php $lc = \Carbon\Carbon::parse($client->last_contacted_at); @endphp
              <span title="{{ $lc->format('d M Y, H:i') }}" style="font-size:.85rem;">
                {{ $lc->diffForHumans() }}
              </span>
            @else
              <span class="text-muted" style="font-size:.82rem;">Never</span>
            @endif
          </td>
          <td data-label="Actions">
            <div class="d-flex gap-2" style="flex-wrap:wrap;">
              <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-secondary">View</a>
              @can('interactions.create')
              <button class="btn btn-sm btn-secondary"
                      data-open-log
                      data-client-id="{{ $client->id }}">Log</button>
              @endcan
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

@can('interactions.create')
  @include('partials.interaction_modals')
@endcan
@endsection
