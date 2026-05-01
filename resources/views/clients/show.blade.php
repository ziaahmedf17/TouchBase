@extends('layouts.app')
@section('title', $client->name)

@section('content')

{{-- Profile header --}}
<div class="profile-header">
  <div class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
  <div style="flex:1">
    <div class="profile-name">{{ $client->name }}</div>
    <div class="profile-meta">
      @if($client->phone)
        <a href="{{ $client->telUrl() }}">{{ $client->phone }}</a> &bull;
      @endif
      @if($client->address) {{ $client->address }} @endif
    </div>
  </div>
  <div class="d-flex gap-2">
    @if($client->phone)
      <a href="{{ $client->telUrl() }}" class="btn btn-success btn-sm">&#128222; Call</a>
      <a href="{{ $client->whatsappUrl() }}" target="_blank" class="btn btn-primary btn-sm">&#128172; WA</a>
      <button class="btn btn-secondary btn-sm" data-copy="{{ $client->phone }}">Copy #</button>
    @endif
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary btn-sm">Edit</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

  {{-- Details --}}
  <div class="card">
    <div class="card-title">Details</div>
    @if($client->next_visit_date)
    <p><strong>Next Visit:</strong> {{ $client->next_visit_date->format('d M Y') }}
      @if($client->visit_reminder_days)
        <span class="text-muted">(remind {{ implode(', ', $client->visit_reminder_days) }}d before)</span>
      @endif
    </p>
    @endif
    @if($client->notes)
    <p class="mt-2" style="white-space:pre-line;">{{ $client->notes }}</p>
    @endif
    @if(!$client->next_visit_date && !$client->notes)
      <p class="text-muted">No additional details.</p>
    @endif
  </div>

  {{-- Recent Alerts --}}
  <div class="card">
    <div class="card-title">Recent Alerts</div>
    @forelse($client->notifications as $n)
      <div class="notif-item {{ $n->is_read ? '' : 'unread' }}" style="border-radius:4px;margin-bottom:.35rem;" data-mark-read="{{ $n->id }}">
        <div class="notif-item-title" style="font-size:.85rem;">{{ $n->message }}</div>
        <div class="notif-item-meta">{{ $n->triggered_date->format('d M Y') }}</div>
      </div>
    @empty
      <p class="text-muted">No alerts yet.</p>
    @endforelse
  </div>
</div>

{{-- Events --}}
<div class="card mt-3">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
    <div class="card-title" style="margin:0;">Life Events</div>
    <a href="{{ route('clients.events.create', $client) }}" class="btn btn-sm btn-primary">+ Add Event</a>
  </div>

  @if($client->events->isEmpty())
    <div class="empty-state" style="padding:1.5rem;">
      <p>No events yet. <a href="{{ route('clients.events.create', $client) }}">Add one</a>.</p>
    </div>
  @else
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Type</th>
          <th>Date</th>
          <th>Annual</th>
          <th>Remind (days)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($client->events as $event)
        <tr>
          <td><span class="badge {{ $event->badgeClass() }}">{{ $event->typeLabel() }}</span></td>
          <td>{{ $event->event_date->format('d M Y') }}</td>
          <td>{{ $event->is_annual ? 'Yes' : 'No' }}</td>
          <td>{{ implode(', ', $event->reminder_days ?? []) ?: '—' }}</td>
          <td>
            <div class="d-flex gap-2">
              <a href="{{ route('clients.events.edit', [$client, $event]) }}" class="btn btn-sm btn-primary">Edit</a>
              <form method="POST" action="{{ route('clients.events.destroy', [$client, $event]) }}"
                    data-confirm="Delete this event and all its alerts?">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Del</button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif
</div>

<div class="mt-3">
  <form method="POST" action="{{ route('clients.destroy', $client) }}"
        data-confirm="Permanently delete {{ $client->name }} and all their data?">
    @csrf @method('DELETE')
    <button class="btn btn-danger btn-sm">Delete Client</button>
  </form>
</div>

@endsection
