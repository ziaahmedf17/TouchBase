@extends('layouts.app')
@section('title', 'Alerts')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">&#128276; Alerts</h1>
    @if($unreadCount > 0)
      <p class="text-muted" style="font-size:.85rem;margin-top:.2rem;">
        {{ $unreadCount }} unread alert{{ $unreadCount == 1 ? '' : 's' }} waiting for action
      </p>
    @endif
  </div>
  @can('notifications.manage')
  <form method="POST" action="{{ route('notifications.readAll') }}">
    @csrf
    <button class="btn btn-secondary btn-sm">&#10003; Mark All Read</button>
  </form>
  @endcan
</div>

{{-- ══════════════════════════════════════════
     UNREAD ALERTS — action cards
     ══════════════════════════════════════════ --}}
@if($unreadCount === 0)
  <div class="empty-state">
    <div class="icon">&#127881;</div>
    <p>You're all caught up! No unread alerts right now.</p>
  </div>
@else

  {{-- Today --}}
  @if($today->isNotEmpty())
  <div class="section-title" style="color:var(--danger);">&#128293; Today ({{ $today->count() }})</div>
  @foreach($today as $n)
    @include('partials.alert_card', ['n' => $n, 'urgent' => true])
  @endforeach
  @endif

  {{-- This Week --}}
  @if($thisWeek->isNotEmpty())
  <div class="section-title" style="{{ $today->isNotEmpty() ? 'margin-top:1.5rem;' : '' }}">
    &#128197; This Week ({{ $thisWeek->count() }})
  </div>
  @foreach($thisWeek as $n)
    @include('partials.alert_card', ['n' => $n, 'urgent' => false])
  @endforeach
  @endif

  {{-- Older --}}
  @if($older->isNotEmpty())
  <div class="section-title" style="{{ ($today->isNotEmpty() || $thisWeek->isNotEmpty()) ? 'margin-top:1.5rem;' : '' }}">
    &#128230; Older ({{ $older->count() }})
  </div>
  @foreach($older as $n)
    @include('partials.alert_card', ['n' => $n, 'urgent' => false])
  @endforeach
  @endif

@endif

{{-- ══════════════════════════════════════════
     PAST ALERTS — read notifications table
     ══════════════════════════════════════════ --}}
@if($readNotifications->isNotEmpty())
<div class="card mt-3" style="padding:0;">
  <div style="padding:.75rem 1rem .5rem;display:flex;align-items:center;justify-content:space-between;">
    <div class="card-title" style="margin:0;">Past Alerts</div>
    <span class="text-muted" style="font-size:.8rem;">{{ $readNotifications->total() }} read</span>
  </div>
  <div class="table-wrap">
    <table class="table-cards">
      <thead>
        <tr>
          <th>Message</th>
          <th>Type</th>
          <th>Date</th>
          <th>Interactions</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach($readNotifications as $n)
        <tr>
          <td data-label="Message">
            <div style="font-size:.9rem;">{{ $n->message }}</div>
            @if($n->client && $n->client->phone)
            <div class="notif-actions mt-1">
              <a href="{{ $n->client->telUrl() }}" class="btn btn-sm btn-success">Call</a>
              <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">WhatsApp</a>
              <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
            </div>
            @endif
          </td>
          <td data-label="Type">
            @if($n->event)
              <span class="badge {{ $n->event->badgeClass() }}">{{ $n->event->typeLabel() }}</span>
            @else
              <span class="text-muted">—</span>
            @endif
          </td>
          <td data-label="Date" style="white-space:nowrap;">{{ $n->triggered_date->format('d M Y') }}</td>
          <td data-label="Interactions">
            @php $count = $n->interactions->count(); @endphp
            @if($count > 0)
              <span class="badge badge-responded">{{ $count }} logged</span>
            @endif
            @can('interactions.create')
            @if($n->client)
            <button class="btn btn-sm btn-secondary"
                    data-open-log
                    data-client-id="{{ $n->client->id }}"
                    data-notification-id="{{ $n->id }}">+ Log</button>
            @endif
            @endcan
          </td>
          <td data-label="Actions">
            @can('notifications.manage')
            <button class="btn btn-sm btn-danger" data-delete-notif="{{ $n->id }}">Delete</button>
            @endcan
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<div>{{ $readNotifications->links('partials.pagination') }}</div>
@endif

@include('partials.interaction_modals')

@push('scripts')
<script>
document.addEventListener('click', function(e) {
  var btn = e.target.closest('[data-done-id]');
  if (!btn) return;
  var id   = btn.dataset.doneId;
  var card = btn.closest('.alert-card');
  btn.disabled    = true;
  btn.textContent = '…';
  fetch('/notifications/' + id + '/read', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
  }).then(function(r) {
    if (r.ok && card) {
      card.style.transition = 'opacity .25s, transform .25s';
      card.style.opacity    = '0';
      card.style.transform  = 'translateX(20px)';
      setTimeout(function() { card.remove(); }, 280);
    } else {
      btn.disabled    = false;
      btn.textContent = '✓ Done';
    }
  }).catch(function() {
    btn.disabled    = false;
    btn.textContent = '✓ Done';
  });
});
</script>
@endpush
@endsection
