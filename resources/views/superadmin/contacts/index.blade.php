@extends('layouts.app')
@section('title', 'Contact Inquiries')

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <h1 class="page-title">&#9993; Contact Inquiries</h1>
  <span class="text-muted" style="font-size:.85rem;">{{ $messages->total() }} total</span>
</div>

@if($messages->isEmpty())
  <div class="empty-state">
    <div class="icon">&#9993;</div>
    <p>No contact inquiries yet.</p>
  </div>
@else
  <div class="card" style="padding:0;overflow:hidden;">
    <table class="table table-cards" style="margin:0;">
      <thead>
        <tr>
          <th>Name</th>
          <th>Email / Phone</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Received</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($messages as $msg)
        <tr style="{{ !$msg->is_read ? 'font-weight:600;background:var(--bg);' : '' }}">
          <td data-label="Name">
            {{ $msg->name }}
            @if(!$msg->is_read)
              <span style="font-size:.68rem;background:var(--primary);color:#fff;
                           padding:.1rem .4rem;border-radius:8px;margin-left:.3rem;font-weight:700;">New</span>
            @endif
          </td>
          <td data-label="Contact">
            <div><a href="mailto:{{ $msg->email }}" style="color:var(--primary);">{{ $msg->email }}</a></div>
            @if($msg->phone)
              <div class="text-muted" style="font-size:.78rem;">{{ $msg->phone }}</div>
            @endif
          </td>
          <td data-label="Subject" class="text-muted" style="font-size:.85rem;">
            {{ $msg->subject ?? '—' }}
          </td>
          <td data-label="Message" style="font-size:.85rem;color:var(--muted);max-width:260px;">
            {{ Str::limit($msg->message, 80) }}
          </td>
          <td data-label="Received" class="text-muted" style="font-size:.82rem;white-space:nowrap;">
            {{ $msg->created_at->format('d M Y') }}<br>
            <span style="font-size:.75rem;">{{ $msg->created_at->format('h:i A') }}</span>
          </td>
          <td data-label="" style="white-space:nowrap;">
            <a href="{{ route('superadmin.contacts.show', $msg) }}" class="btn btn-sm btn-primary">View</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div style="margin-top:1rem;">
    {{ $messages->links('partials.pagination') }}
  </div>
@endif
@endsection
