@forelse($recentNotifications as $n)
<div class="notif-item {{ $n->is_read ? '' : 'unread' }}" style="border-radius:6px;margin-bottom:.5rem;" data-mark-read="{{ $n->id }}">
  <div class="d-flex" style="justify-content:space-between;align-items:flex-start;">
    <div>
      <div class="notif-item-title">{{ $n->message }}</div>
      <div class="notif-item-meta">
        <span class="badge {{ $n->event?->badgeClass() }}">{{ $n->event?->typeLabel() }}</span>
        &bull; {{ $n->triggered_date->format('d M Y') }}
      </div>
    </div>
    <button data-delete-notif="{{ $n->id }}" class="btn btn-icon btn-secondary" title="Dismiss">&times;</button>
  </div>
  @if($n->client && $n->client->phone)
  <div class="notif-actions mt-2">
    <a href="{{ $n->client->telUrl() }}"       class="btn btn-sm btn-success">&#128222; Call</a>
    <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">&#128172; WhatsApp</a>
    <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
  </div>
  @endif
</div>
@empty
<div class="empty-state">
  <div class="icon">&#128276;</div>
  <p>No alerts yet. Click <strong>Update Alerts</strong> to check.</p>
</div>
@endforelse

@if(isset($recentNotifications) && $recentNotifications->count() > 0)
<div style="text-align:right;margin-top:.75rem;">
  <a href="{{ route('notifications.index') }}" class="btn btn-secondary btn-sm">View all alerts &rarr;</a>
</div>
@endif
