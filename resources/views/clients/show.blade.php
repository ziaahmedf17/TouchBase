@extends('layouts.app')
@section('title', $client->name)

@section('content')

{{-- Profile header --}}
<div class="profile-header">
  <div class="avatar">{{ strtoupper(substr($client->name, 0, 1)) }}</div>
  <div class="profile-header-info">
    <div class="profile-name">{{ $client->name }}</div>
    <div class="profile-meta">
      @if($client->phone)
        <a href="{{ $client->telUrl() }}">{{ $client->phone }}</a> &bull;
      @endif
      @if($client->address) {{ $client->address }} @endif
    </div>
  </div>
  <div class="profile-header-actions">
    @if($client->phone)
      <a href="{{ $client->telUrl() }}" class="btn btn-success btn-sm">&#128222; Call</a>
      <a href="{{ $client->whatsappUrl() }}" target="_blank" class="btn btn-primary btn-sm">&#128172; WA</a>
      <button class="btn btn-secondary btn-sm" data-copy="{{ $client->phone }}">Copy #</button>
    @endif
    <button class="btn btn-secondary btn-sm"
            data-open-log
            data-client-id="{{ $client->id }}">
      + Log
    </button>
    <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary btn-sm">Edit</a>
  </div>
</div>

<div class="grid-2col">

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

{{-- Interactions --}}
<div class="card mt-3">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
    <div class="card-title" style="margin:0;">
      Interactions
      @if($client->interactions->isNotEmpty())
        <span class="text-muted" style="font-size:.8rem;font-weight:400;margin-left:.4rem;">({{ $client->interactions->count() }})</span>
      @endif
    </div>
    <button class="btn btn-sm btn-primary"
            data-open-log
            data-client-id="{{ $client->id }}">
      + Log Interaction
    </button>
  </div>

  @if($client->interactions->isEmpty())
    <div class="empty-state" style="padding:1.5rem;">
      <p>No interactions logged yet. Use <strong>+ Log Interaction</strong> after reaching out.</p>
    </div>
  @else
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Date</th>
          <th>Type</th>
          <th>Status</th>
          <th>Notes</th>
          <th>Response</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($client->interactions as $ix)
        <tr>
          <td data-label="Date" style="white-space:nowrap;">
            {{ $ix->contacted_at->format('d M Y') }}
            <span class="text-muted" style="font-size:.75rem;">{{ $ix->contacted_at->format('H:i') }}</span>
          </td>
          <td data-label="Type"><span class="badge {{ $ix->typeBadgeClass() }}">{{ $ix->typeLabel() }}</span></td>
          <td data-label="Status"><span class="badge {{ $ix->statusBadgeClass() }}">{{ $ix->statusLabel() }}</span></td>
          <td data-label="Notes">
            <div>
              @if($ix->notes)
                <span style="font-size:.85rem;">{{ $ix->notes }}</span>
              @else
                <span class="text-muted">—</span>
              @endif
              @if($ix->notification)
                <div style="font-size:.75rem;color:var(--muted);margin-top:.2rem;">
                  Re: {{ $ix->notification->message }}
                </div>
              @endif
            </div>
          </td>
          <td data-label="Response">
            <div>
              @if($ix->response_notes)
                <span style="font-size:.85rem;">{{ $ix->response_notes }}</span>
                @if($ix->response_at)
                  <div class="text-muted" style="font-size:.75rem;">{{ $ix->response_at->format('d M Y H:i') }}</div>
                @endif
              @else
                <span class="text-muted" style="font-size:.8rem;">No response logged</span>
              @endif
            </div>
          </td>
          <td data-label="Actions">
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-secondary"
                      data-open-response
                      data-interaction-id="{{ $ix->id }}"
                      data-status="{{ $ix->status }}"
                      data-response-notes="{{ $ix->response_notes ?? '' }}"
                      data-response-at="{{ $ix->response_at ? $ix->response_at->format('Y-m-d\TH:i') : '' }}">
                Update
              </button>
              <form method="POST" action="{{ route('interactions.destroy', $ix) }}"
                    data-confirm="Delete this interaction?">
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
    <table class="table-cards">
      <thead>
        <tr>
          <th>Type</th>
          <th>Date</th>
          <th>Recurrence</th>
          <th>Remind (days)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($client->events as $event)
        <tr>
          <td data-label="Type"><span class="badge {{ $event->badgeClass() }}">{{ $event->typeLabel() }}</span></td>
          <td data-label="Date">{{ $event->event_date->format('d M Y') }}</td>
          <td data-label="Recurrence">{{ $event->recurrenceLabel() }}</td>
          <td data-label="Remind">{{ implode(', ', $event->reminder_days ?? []) ?: '—' }}</td>
          <td data-label="Actions">
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

@include('partials.interaction_modals')
@endsection
