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
    <a href="#contact" class="hide-mobile">Contact</a>
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

{{-- ── Contact ───────────────────────────────── --}}
<section class="lp-section lp-section-alt" id="contact">
  <div class="lp-container" style="max-width:640px;">
    <div class="lp-section-label">Contact</div>
    <h2 class="lp-section-title">Get in touch</h2>
    <p class="lp-section-sub">Have a question or need help? Send us a message and we'll get back to you.</p>

    @if(session('contact_success'))
      <div style="background:#dcfce7;color:#166534;border:1px solid #86efac;border-radius:8px;
                  padding:.85rem 1.1rem;margin-bottom:1.5rem;font-weight:600;">
        &#10003; Your message has been sent! We'll be in touch soon.
      </div>
    @endif

    <form method="POST" action="{{ route('contact.store') }}"
          style="background:#fff;border-radius:12px;padding:2rem;box-shadow:0 1px 4px rgba(0,0,0,.08);">
      @csrf

      <div class="grid-2col" style="gap:1rem;margin-bottom:1rem;">
        <div>
          <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:.3rem;color:#374151;">
            Your Name <span style="color:#dc2626;">*</span>
          </label>
          <input type="text" name="name" value="{{ old('name') }}" required
                 placeholder="Ali Khan"
                 style="width:100%;padding:.6rem .8rem;border:1px solid #d1d5db;border-radius:7px;font-size:.9rem;box-sizing:border-box;">
          @error('name')<div style="color:#dc2626;font-size:.78rem;margin-top:.2rem;">{{ $message }}</div>@enderror
        </div>
        <div>
          <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:.3rem;color:#374151;">
            Email Address <span style="color:#dc2626;">*</span>
          </label>
          <input type="email" name="email" value="{{ old('email') }}" required
                 placeholder="ali@example.com"
                 style="width:100%;padding:.6rem .8rem;border:1px solid #d1d5db;border-radius:7px;font-size:.9rem;box-sizing:border-box;">
          @error('email')<div style="color:#dc2626;font-size:.78rem;margin-top:.2rem;">{{ $message }}</div>@enderror
        </div>
      </div>

      <div class="grid-2col" style="gap:1rem;margin-bottom:1rem;">
        <div>
          <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:.3rem;color:#374151;">Phone</label>
          <input type="text" name="phone" value="{{ old('phone') }}"
                 placeholder="+92 300 0000000"
                 style="width:100%;padding:.6rem .8rem;border:1px solid #d1d5db;border-radius:7px;font-size:.9rem;box-sizing:border-box;">
        </div>
        <div>
          <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:.3rem;color:#374151;">Subject</label>
          <input type="text" name="subject" value="{{ old('subject') }}"
                 placeholder="Pricing question"
                 style="width:100%;padding:.6rem .8rem;border:1px solid #d1d5db;border-radius:7px;font-size:.9rem;box-sizing:border-box;">
        </div>
      </div>

      <div style="margin-bottom:1.25rem;">
        <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:.3rem;color:#374151;">
          Message <span style="color:#dc2626;">*</span>
        </label>
        <textarea name="message" id="contact-message" required rows="5" maxlength="2000"
                  placeholder="Write your message here..."
                  style="width:100%;padding:.6rem .8rem;border:1px solid #d1d5db;border-radius:7px;font-size:.9rem;
                         box-sizing:border-box;resize:vertical;">{{ old('message') }}</textarea>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:.25rem;">
          @error('message')
            <div style="color:#dc2626;font-size:.78rem;">{{ $message }}</div>
          @else
            <div></div>
          @enderror
          <div id="contact-msg-count" style="font-size:.75rem;color:#6b7280;">
            <span id="contact-msg-used">{{ strlen(old('message', '')) }}</span> / 2000
          </div>
        </div>
      </div>

      <button type="submit"
              style="background:#2563eb;color:#fff;border:none;padding:.7rem 2rem;border-radius:7px;
                     font-size:.95rem;font-weight:600;cursor:pointer;width:100%;">
        Send Message &#8594;
      </button>
    </form>

    <p style="text-align:center;margin-top:1.25rem;font-size:.85rem;color:#64748b;">
      Or email us directly at
      <a href="mailto:touchbase@genwizz.com" style="color:#2563eb;">touchbase@genwizz.com</a>
    </p>
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
    <div style="display:flex;gap:.75rem;align-items:center;">
      <a href="https://www.linkedin.com/company/genwizz/" target="_blank" rel="noopener"
         title="LinkedIn" style="color:#94a3b8;text-decoration:none;font-size:.82rem;display:flex;align-items:center;gap:.3rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M20.447 20.452H17.21v-5.569c0-1.328-.024-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.987V9h3.102v1.561h.044c.431-.817 1.485-1.678 3.057-1.678 3.269 0 3.873 2.151 3.873 4.948v6.621zM5.337 7.433a1.803 1.803 0 1 1 0-3.606 1.803 1.803 0 0 1 0 3.606zM6.959 20.452H3.713V9h3.246v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
        </svg>
        LinkedIn
      </a>
      <a href="https://www.facebook.com/GenWizz" target="_blank" rel="noopener"
         title="Facebook" style="color:#94a3b8;text-decoration:none;font-size:.82rem;display:flex;align-items:center;gap:.3rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
          <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.413c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
        </svg>
        Facebook
      </a>
    </div>
    <div style="font-size:.82rem;line-height:1.8;text-align:right;">
      <div>
        &#9993; <a href="mailto:touchbase@genwizz.com"
                   style="color:#94a3b8;text-decoration:none;">touchbase@genwizz.com</a>
      </div>
      <div style="margin-top:.2rem;font-size:.78rem;color:#64748b;">
        &copy; {{ date('Y') }} TouchBase. All rights reserved.<br>
        A product of <a href="https://genwizz.com" target="_blank" rel="noopener"
                        style="color:#64748b;text-decoration:none;">Genwizz.com</a>
      </div>
    </div>
  </div>
</footer>

@endsection

@push('scripts')
<script>
  // Contact message character counter
  var msgArea  = document.getElementById('contact-message');
  var msgCount = document.getElementById('contact-msg-used');
  if (msgArea && msgCount) {
    msgArea.addEventListener('input', function () {
      var len = msgArea.value.length;
      msgCount.textContent = len;
      msgCount.style.color = len >= 1900 ? '#dc2626' : len >= 1600 ? '#d97706' : '#6b7280';
    });
  }

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
