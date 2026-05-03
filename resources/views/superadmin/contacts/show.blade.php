@extends('layouts.app')
@section('title', 'Inquiry from ' . $contact->name)

@section('content')
@include('partials.superadmin_nav')

<div class="page-header">
  <div>
    <a href="{{ route('superadmin.contacts.index') }}"
       style="font-size:.82rem;color:var(--muted);text-decoration:none;">&#8592; All Inquiries</a>
    <h1 class="page-title" style="margin-top:.25rem;">&#9993; Inquiry from {{ $contact->name }}</h1>
  </div>
  <form method="POST" action="{{ route('superadmin.contacts.destroy', $contact) }}"
        onsubmit="return confirm('Delete this inquiry?')">
    @csrf @method('DELETE')
    <button type="submit" class="btn btn-danger">Delete</button>
  </form>
</div>

<div class="grid-sidebar">

  {{-- Message body --}}
  <div class="card">
    <div class="card-title">Message</div>
    @if($contact->subject)
      <div style="font-size:.82rem;color:var(--muted);margin-bottom:.75rem;text-transform:uppercase;
                  letter-spacing:.04em;">{{ $contact->subject }}</div>
    @endif
    <div style="font-size:.95rem;line-height:1.75;color:var(--text);white-space:pre-wrap;">{{ $contact->message }}</div>
  </div>

  {{-- Sender details --}}
  <div>
    <div class="card" style="margin-bottom:1rem;">
      <div class="card-title">Sender</div>
      <div style="display:grid;gap:.65rem;">

        <div>
          <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Name</div>
          <div style="font-weight:600;">{{ $contact->name }}</div>
        </div>

        <div>
          <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Email</div>
          <div>
            <a href="mailto:{{ $contact->email }}" style="color:var(--primary);font-weight:600;">
              {{ $contact->email }}
            </a>
          </div>
        </div>

        @if($contact->phone)
        <div>
          <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Phone</div>
          <div>
            <a href="tel:{{ $contact->phone }}" style="color:var(--text);font-weight:600;">
              {{ $contact->phone }}
            </a>
          </div>
        </div>
        @endif

        @if($contact->subject)
        <div>
          <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Subject</div>
          <div>{{ $contact->subject }}</div>
        </div>
        @endif

        <div>
          <div class="text-muted" style="font-size:.72rem;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.15rem;">Received</div>
          <div>{{ $contact->created_at->format('d M Y, h:i A') }}</div>
          <div class="text-muted" style="font-size:.78rem;">{{ $contact->created_at->diffForHumans() }}</div>
        </div>

      </div>
    </div>

    <div class="d-flex gap-2" style="flex-direction:column;">
      <a href="mailto:{{ $contact->email }}" class="btn btn-primary" style="text-align:center;">
        &#9993; Reply via Email
      </a>
      @if($contact->phone)
        <a href="tel:{{ $contact->phone }}" class="btn btn-secondary" style="text-align:center;">
          &#128222; Call
        </a>
      @endif
    </div>
  </div>

</div>
@endsection
