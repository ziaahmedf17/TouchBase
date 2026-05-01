@extends('layouts.app')
@section('title', 'Calendar')

@section('content')
<div class="calendar-header">
  <a href="{{ route('calendar.index', ['year' => $prev->year, 'month' => $prev->month]) }}"
     class="btn btn-secondary">&larr;</a>

  <div class="calendar-title">{{ $current->format('F Y') }}</div>

  <a href="{{ route('calendar.index', ['year' => $next->year, 'month' => $next->month]) }}"
     class="btn btn-secondary">&rarr;</a>
</div>

{{-- Day names --}}
<div class="cal-grid" style="margin-bottom:2px;">
  @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $d)
    <div class="cal-day-name">{{ $d }}</div>
  @endforeach
</div>

{{-- Calendar cells --}}
<div class="cal-grid">
  {{-- Leading empty cells --}}
  @for($i = 0; $i < $startDow; $i++)
    <div class="cal-cell empty"></div>
  @endfor

  @for($day = 1; $day <= $daysInMonth; $day++)
    @php
      $isToday = now()->year === $year && now()->month === $month && now()->day === $day;
      $dayEvents = $calendarData[$day] ?? [];
      // Serialize events for JS click handler
      $eventsJson = json_encode(collect($dayEvents)->map(fn($e) => [
        'client'     => $e->client?->name ?? '—',
        'client_url' => $e->client ? route('clients.show', $e->client) : null,
        'type'       => $e->typeLabel(),
        'badge'      => $e->badgeClass(),
        'label'      => $e->label,
        'phone'      => $e->client?->phone,
      ])->values()->all());
    @endphp

    <div class="cal-cell {{ $isToday ? 'today' : '' }}"
         data-day="{{ sprintf('%02d', $day) . ' ' . $current->format('M Y') }}"
         data-events="{{ htmlspecialchars($eventsJson, ENT_QUOTES) }}">
      <div class="cal-date">{{ $day }}</div>

      @foreach($dayEvents as $ev)
        @if($ev->client)
          <a href="{{ route('clients.show', $ev->client) }}"
             class="cal-event {{ $ev->badgeClass() }}"
             onclick="event.stopPropagation()"
             title="{{ $ev->client->name }} — {{ $ev->typeLabel() }}">
            {{ $ev->client->name }} — {{ $ev->typeLabel() }}
          </a>
        @else
          <span class="cal-event {{ $ev->badgeClass() }}">{{ $ev->typeLabel() }}</span>
        @endif
      @endforeach
    </div>
  @endfor
</div>

{{-- Legend --}}
<div class="d-flex gap-2 mt-3" style="flex-wrap:wrap;">
  <span class="badge badge-birthday">Birthday</span>
  <span class="badge badge-anniversary">Anniversary</span>
  <span class="badge badge-visit">Visit</span>
  <span class="badge badge-custom">Custom</span>
</div>

{{-- Modal for day events --}}
<div id="cal-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:300;align-items:center;justify-content:center;">
  <div class="card" style="max-width:420px;width:90%;max-height:80vh;overflow-y:auto;position:relative;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:.75rem;">
      <strong id="cal-modal-title">Events</strong>
      <button id="cal-modal-close" class="btn btn-icon btn-secondary">&times;</button>
    </div>
    <div id="cal-modal-body"></div>
  </div>
</div>

@push('scripts')
<script>
  // Update data-day with full label per cell
  document.querySelectorAll('.cal-cell[data-day]').forEach(function(cell, idx) {
    // Already set in Blade — nothing extra needed
  });
</script>
@endpush
@endsection
