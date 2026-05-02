@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="page-header">
  <h1 class="page-title">All Alerts</h1>
  <div class="d-flex gap-2">
    @can('notifications.manage')
    <form method="POST" action="{{ route('notifications.readAll') }}">
      @csrf
      <button class="btn btn-secondary btn-sm">Mark All Read</button>
    </form>
    @endcan
  </div>
</div>

@if($notifications->isEmpty())
  <div class="empty-state">
    <div class="icon">&#128276;</div>
    <p>No notifications yet. Open the dashboard and click <strong>Update Alerts</strong>.</p>
  </div>
@else
<div class="card" style="padding:0;">
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Status</th>
          <th>Message</th>
          <th>Type</th>
          <th>Triggered</th>
          <th>Interactions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($notifications as $n)
        <tr class="{{ $n->is_read ? '' : 'unread' }}" style="{{ $n->is_read ? '' : 'background:#eff6ff;' }}">
          <td data-label="Status">
            @if(!$n->is_read)
              <span class="badge badge-custom">New</span>
            @else
              <span class="text-muted" style="font-size:.8rem;">Read</span>
            @endif
          </td>
          <td data-label="Message">
            <div>
              <div style="font-weight:{{ $n->is_read ? '400' : '600' }}">{{ $n->message }}</div>
              @if($n->client && $n->client->phone)
              <div class="notif-actions mt-1">
                <a href="{{ $n->client->telUrl() }}" class="btn btn-sm btn-success">Call</a>
                <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">WhatsApp</a>
                <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
              </div>
              @endif
            </div>
          </td>
          <td data-label="Type">
            @if($n->event)
              <span class="badge {{ $n->event->badgeClass() }}">{{ $n->event->typeLabel() }}</span>
            @endif
          </td>
          <td data-label="Triggered">{{ $n->triggered_date->format('d M Y') }}</td>
          <td data-label="Interactions">
            <div>
              @php $count = $n->interactions->count(); @endphp
              @if($count > 0)
                <span class="badge badge-responded" style="margin-bottom:.3rem;">{{ $count }} logged</span>
              @endif
              @can('interactions.create')
              @if($n->client)
              <button class="btn btn-sm btn-secondary"
                      data-open-log
                      data-client-id="{{ $n->client->id }}"
                      data-notification-id="{{ $n->id }}">
                + Log
              </button>
              @endif
              @endcan
            </div>
          </td>
          <td data-label="Actions">
            <div class="d-flex gap-2">
              @can('notifications.manage')
              @if(!$n->is_read)
                <button class="btn btn-sm btn-secondary" data-mark-read="{{ $n->id }}">Read</button>
              @endif
              <button class="btn btn-sm btn-danger" data-delete-notif="{{ $n->id }}">Delete</button>
              @endcan
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div>{{ $notifications->links('partials.pagination') }}</div>
@endif

@include('partials.interaction_modals')
@endsection
