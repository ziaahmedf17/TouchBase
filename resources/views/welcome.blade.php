@extends('layouts.landing')
@section('title', 'TouchBase')

@section('content')

{{-- ── Navigation ───────────────────────────── --}}
<nav class="lp-nav">
  <a href="{{ route('home') }}" class="lp-nav-brand">&#9679; TouchBase</a>
  <div class="lp-nav-links">
    <a href="#features" class="hide-mobile">Features</a>
    <a href="#how-it-works" class="hide-mobile">How It Works</a>
    <a href="#pricing" class="hide-mobile">Pricing</a>
    <a href="{{ route('login') }}" style="color:#2563eb;font-weight:600;">Sign In</a>
    <a href="{{ route('register') }}"
       style="background:#2563eb;color:#fff;padding:.4rem 1rem;border-radius:7px;font-weight:600;font-size:.88rem;text-decoration:none;">
      Get Started
    </a>
  </div>
</nav>

{{-- ── Hero ─────────────────────────────────── --}}
<section class="lp-hero">
  <div class="lp-hero-badge">&#127775; Built for Pakistani Businesses</div>
  <h1 class="lp-hero-title">
    Manage Clients.<br>
    Never Miss a Moment.
  </h1>
  <p class="lp-hero-sub">
    TouchBase is a smart CRM that keeps your client relationships strong — birthday reminders, visit tracking, team collaboration, and more. All in one place.
  </p>
  <div class="lp-hero-cta">
    <a href="{{ route('register') }}" class="lp-btn-white">Get Started Free</a>
    <a href="#how-it-works" class="lp-btn-outline">See How It Works</a>
  </div>
</section>

{{-- ── Features ─────────────────────────────── --}}
<section class="lp-section" id="features">
  <div class="lp-container">
    <div class="lp-section-label">Features</div>
    <h2 class="lp-section-title">Everything you need to grow client relationships</h2>
    <p class="lp-section-sub">From first contact to long-term loyalty — TouchBase handles the details so you can focus on your business.</p>

    <div class="lp-features-grid">
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128101;</div>
        <div class="lp-feature-title">Client Management</div>
        <p class="lp-feature-text">Store every client's name, phone, address, notes, and next visit date in one clean profile. Search and filter instantly.</p>
      </div>
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128276;</div>
        <div class="lp-feature-title">Smart Reminders</div>
        <p class="lp-feature-text">Automatic alerts for birthdays, anniversaries, and upcoming visits — days in advance. Never forget an important date again.</p>
      </div>
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128197;</div>
        <div class="lp-feature-title">Calendar View</div>
        <p class="lp-feature-text">See all your client events in a monthly calendar. Spot busy periods, plan ahead, and stay on top of every commitment.</p>
      </div>
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128101;</div>
        <div class="lp-feature-title">Team Collaboration</div>
        <p class="lp-feature-text">Add managers and staff as sub-users under your account. Control what each person can see and do with role-based permissions.</p>
      </div>
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128172;</div>
        <div class="lp-feature-title">Interaction Tracking</div>
        <p class="lp-feature-text">Log every call, WhatsApp message, and visit. Know exactly when you last contacted a client and what was discussed.</p>
      </div>
      <div class="lp-feature-card">
        <div class="lp-feature-icon">&#128274;</div>
        <div class="lp-feature-title">Secure & Private</div>
        <p class="lp-feature-text">Your data is fully isolated from other businesses. No shared data, no risk — each account has its own private workspace.</p>
      </div>
    </div>
  </div>
</section>

{{-- ── How it works ─────────────────────────── --}}
<section class="lp-section lp-section-alt" id="how-it-works">
  <div class="lp-container">
    <div class="lp-section-label">How It Works</div>
    <h2 class="lp-section-title">Up and running in minutes</h2>
    <p class="lp-section-sub">No technical setup required. Register, pay, and you're in.</p>

    <div class="lp-steps">
      <div class="lp-step">
        <div class="lp-step-num">1</div>
        <div>
          <div class="lp-step-title">Register your business</div>
          <p class="lp-step-text">Fill in your business details, choose a subscription plan, and transfer the payment to our bank account.</p>
        </div>
      </div>
      <div class="lp-step">
        <div class="lp-step-num">2</div>
        <div>
          <div class="lp-step-title">Upload payment screenshot</div>
          <p class="lp-step-text">Take a screenshot of your bank transfer and upload it. Our team reviews and approves accounts within a few hours.</p>
        </div>
      </div>
      <div class="lp-step">
        <div class="lp-step-num">3</div>
        <div>
          <div class="lp-step-title">Start managing clients</div>
          <p class="lp-step-text">Once approved, log in and start adding clients, setting up reminders, and building your team. That's it.</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── Pricing ───────────────────────────────── --}}
<section class="lp-section" id="pricing">
  <div class="lp-container">
    <div class="lp-section-label">Pricing</div>
    <h2 class="lp-section-title">Simple, transparent pricing</h2>
    <p class="lp-section-sub">No hidden fees. Pay once and get full access. Switch plans anytime.</p>

    <div class="lp-pricing-grid">

      {{-- Monthly --}}
      @if(isset($plans['monthly']))
      @php $p = $plans['monthly']; @endphp
      <div class="lp-plan-card">
        <div class="lp-plan-name">{{ $p->name }}</div>
        <div class="lp-plan-price">
          Rs. {{ number_format($p->price, 0) }}
        </div>
        <div class="lp-plan-period">per month &bull; billed monthly</div>
        <ul class="lp-plan-features">
          <li>Unlimited clients</li>
          <li>Smart event reminders</li>
          <li>Calendar view</li>
          <li>Team sub-users</li>
          <li>Support tickets</li>
        </ul>
        <a href="{{ route('register') }}" class="lp-plan-cta lp-plan-cta--outline">Get Started</a>
      </div>
      @endif

      {{-- Yearly (most popular) --}}
      @if(isset($plans['yearly']))
      @php $p = $plans['yearly']; @endphp
      <div class="lp-plan-card lp-plan-card--popular">
        <div class="lp-plan-badge">&#11088; Most Popular</div>
        <div class="lp-plan-name">{{ $p->name }}</div>
        <div class="lp-plan-price">
          Rs. {{ number_format($p->price, 0) }}
        </div>
        <div class="lp-plan-period">
          per year &bull;
          <span style="color:#16a34a;font-weight:600;">
            Save Rs. {{ number_format(($plans['monthly']->price ?? 0) * 12 - $p->price, 0) }} vs monthly
          </span>
        </div>
        <ul class="lp-plan-features">
          <li>Everything in Monthly</li>
          <li>Priority support</li>
          <li>Early access to new features</li>
          <li>Dedicated onboarding call</li>
          <li>Best value for growing teams</li>
        </ul>
        <a href="{{ route('register') }}" class="lp-plan-cta">Get Started</a>
      </div>
      @endif

      {{-- Lifetime --}}
      @if(isset($plans['lifetime']))
      @php $p = $plans['lifetime']; @endphp
      <div class="lp-plan-card">
        <div class="lp-plan-name">{{ $p->name }}</div>
        <div class="lp-plan-price">
          Rs. {{ number_format($p->price, 0) }}
        </div>
        <div class="lp-plan-period">one-time payment &bull; never pay again</div>
        <ul class="lp-plan-features">
          <li>Everything in Yearly</li>
          <li>Lifetime updates included</li>
          <li>No renewals ever</li>
          <li>Lock in today's price forever</li>
          <li>Best long-term investment</li>
        </ul>
        <a href="{{ route('register') }}" class="lp-plan-cta lp-plan-cta--outline">Get Started</a>
      </div>
      @endif

    </div>

    <p style="text-align:center;margin-top:2rem;font-size:.88rem;color:#64748b;">
      All plans include full access to all features. Prices are in Pakistani Rupees (Rs.).<br>
      Payment is manual — transfer to our bank account and upload a screenshot.
    </p>
  </div>
</section>

{{-- ── CTA Banner ───────────────────────────── --}}
<section style="background:linear-gradient(135deg,#1e3a8a,#2563eb);padding:5rem 2rem;text-align:center;color:#fff;">
  <div style="max-width:560px;margin:0 auto;">
    <h2 style="font-size:2rem;font-weight:800;margin:0 0 1rem;letter-spacing:-.02em;">
      Ready to take control of your client relationships?
    </h2>
    <p style="color:rgba(255,255,255,.85);font-size:1rem;line-height:1.7;margin:0 0 2rem;">
      Join hundreds of businesses already using TouchBase to grow stronger client bonds.
    </p>
    <a href="{{ route('register') }}" class="lp-btn-white">Create Your Account</a>
  </div>
</section>

{{-- ── Footer ───────────────────────────────── --}}
<footer class="lp-footer">
  <div class="lp-footer-inner">
    <div>
      <div class="lp-footer-brand">&#9679; TouchBase</div>
      <div style="font-size:.82rem;margin-top:.35rem;">Smart CRM for growing businesses</div>
    </div>
    <div class="lp-footer-links">
      <a href="#features">Features</a>
      <a href="#pricing">Pricing</a>
      <a href="{{ route('login') }}">Sign In</a>
      <a href="{{ route('register') }}">Register</a>
    </div>
    <div style="font-size:.8rem;">
      &copy; {{ date('Y') }} TouchBase. All rights reserved.
    </div>
  </div>
</footer>

@endsection

@push('scripts')
<script>
  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
</script>
@endpush
