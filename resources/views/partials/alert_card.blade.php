<div class="alert-card card" style="margin-bottom:.75rem;{{ $urgent ? 'border-left:4px solid var(--danger);' : '' }}">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.75rem;margin-bottom:.6rem;">

    {{-- Left: client info --}}
    <div style="flex:1;min-width:0;">
      <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.25rem;">
        @if($n->event)
          <span class="badge {{ $n->event->badgeClass() }}">{{ $n->event->typeLabel() }}</span>
        @endif
        <a href="{{ route('clients.show', $n->client) }}"
           style="font-weight:700;font-size:.95rem;color:var(--text);text-decoration:none;">
          {{ $n->client->name }}
        </a>
        @if($n->client->gender)
          <span class="text-muted" style="font-size:.75rem;">{{ ucfirst($n->client->gender) }}</span>
        @endif
      </div>
      @if($n->client->phone)
        <div style="font-size:.82rem;color:var(--muted);margin-bottom:.5rem;">
          &#128222; {{ $n->client->phone }}
        </div>
      @endif

      {{-- Pre-written message --}}
      <div style="background:var(--bg);border-left:3px solid var(--primary);padding:.5rem .75rem;
                  border-radius:0 var(--radius) var(--radius) 0;font-size:.85rem;line-height:1.6;
                  margin-bottom:.1rem;color:var(--text);white-space:pre-line;">{{ $n->whatsappMessage() }}</div>
    </div>

    {{-- Right: time --}}
    <div style="font-size:.75rem;color:var(--muted);text-align:right;flex-shrink:0;white-space:nowrap;">
      {{ $n->triggered_date->diffForHumans() }}
    </div>
  </div>

  {{-- Actions --}}
  <div class="notif-actions" style="flex-wrap:wrap;">
    @if($n->client->phone)
      <a href="{{ $n->client->telUrl() }}" class="btn btn-sm btn-success">&#128222; Call</a>
      <a href="{{ $n->client->whatsappUrl() }}" target="_blank" class="btn btn-sm btn-primary">&#128172; WhatsApp</a>
      <button class="btn btn-sm btn-secondary" data-copy="{{ $n->client->phone }}">Copy #</button>
    @endif
    <button class="btn btn-sm btn-secondary" data-copy="{{ $n->whatsappMessage() }}"
            title="Copy pre-written message">&#128172; Copy Msg</button>
    @can('interactions.create')
    <button class="btn btn-sm btn-secondary"
            data-open-log
            data-client-id="{{ $n->client->id }}"
            data-notification-id="{{ $n->id }}">+ Log</button>
    @endcan
    @can('notifications.manage')
    <button class="btn btn-sm btn-success" data-done-id="{{ $n->id }}">&#10003; Done</button>
    @endcan
  </div>
</div>
