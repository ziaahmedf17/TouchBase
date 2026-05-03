<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'TouchBase') — Smart CRM for Growing Businesses</title>
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    * { box-sizing: border-box; }
    body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #0f172a; }

    /* Landing nav */
    .lp-nav {
      position: sticky; top: 0; z-index: 100;
      display: flex; align-items: center; justify-content: space-between;
      padding: 0 2rem; height: 60px;
      background: rgba(255,255,255,.95); backdrop-filter: blur(8px);
      border-bottom: 1px solid #e2e8f0;
    }
    .lp-nav-brand { font-size: 1.2rem; font-weight: 800; color: #2563eb; text-decoration: none; letter-spacing: -.02em; }
    .lp-nav-links { display: flex; align-items: center; gap: 1.5rem; }
    .lp-nav-links a { font-size: .9rem; color: #475569; text-decoration: none; font-weight: 500; }
    .lp-nav-links a:hover { color: #2563eb; }

    /* Sections */
    .lp-section { padding: 5rem 2rem; }
    .lp-section-alt { background: #f8fafc; }
    .lp-container { max-width: 1100px; margin: 0 auto; }
    .lp-section-label { font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #2563eb; margin-bottom: .6rem; }
    .lp-section-title { font-size: 2rem; font-weight: 800; line-height: 1.2; margin: 0 0 1rem; }
    .lp-section-sub { font-size: 1.05rem; color: #475569; max-width: 560px; line-height: 1.7; margin: 0; }

    /* Hero */
    .lp-hero {
      background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #3b82f6 100%);
      color: #fff; text-align: center; padding: 7rem 2rem 6rem;
    }
    .lp-hero-badge {
      display: inline-block; background: rgba(255,255,255,.15); color: #fff;
      border: 1px solid rgba(255,255,255,.3); border-radius: 999px;
      padding: .3rem 1rem; font-size: .78rem; font-weight: 600; margin-bottom: 1.5rem;
    }
    .lp-hero-title { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; margin: 0 0 1.25rem; line-height: 1.1; letter-spacing: -.03em; }
    .lp-hero-sub { font-size: 1.1rem; color: rgba(255,255,255,.85); max-width: 520px; margin: 0 auto 2.5rem; line-height: 1.7; }
    .lp-hero-cta { display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap; }
    .lp-btn-white {
      background: #fff; color: #2563eb; font-weight: 700; font-size: .95rem;
      padding: .75rem 2rem; border-radius: 8px; text-decoration: none; border: none;
      transition: transform .15s, box-shadow .15s;
    }
    .lp-btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,.15); }
    .lp-btn-outline {
      background: transparent; color: #fff; font-weight: 600; font-size: .95rem;
      padding: .75rem 2rem; border-radius: 8px; text-decoration: none;
      border: 2px solid rgba(255,255,255,.5); transition: background .15s, border-color .15s;
    }
    .lp-btn-outline:hover { background: rgba(255,255,255,.1); border-color: #fff; }

    /* Feature grid */
    .lp-features-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1.25rem; margin-top: 3rem; }
    .lp-feature-card {
      background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
      padding: 1.75rem 1.5rem; transition: box-shadow .2s, transform .2s;
    }
    .lp-feature-card:hover { box-shadow: 0 8px 32px rgba(37,99,235,.08); transform: translateY(-3px); }
    .lp-feature-icon { font-size: 2rem; margin-bottom: .9rem; }
    .lp-feature-title { font-size: 1rem; font-weight: 700; margin: 0 0 .5rem; }
    .lp-feature-text { font-size: .88rem; color: #64748b; line-height: 1.6; margin: 0; }

    /* Steps */
    .lp-steps { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem; margin-top: 3rem; }
    .lp-step { display: flex; gap: 1.25rem; align-items: flex-start; }
    .lp-step-num {
      flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%;
      background: #2563eb; color: #fff; display: flex; align-items: center;
      justify-content: center; font-weight: 800; font-size: 1rem;
    }
    .lp-step-title { font-size: 1rem; font-weight: 700; margin: 0 0 .4rem; }
    .lp-step-text { font-size: .88rem; color: #64748b; line-height: 1.6; margin: 0; }

    /* Pricing */
    .lp-pricing-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-top: 3rem; }
    .lp-plan-card {
      background: #fff; border: 2px solid #e2e8f0; border-radius: 16px;
      padding: 2rem 1.75rem; position: relative; transition: box-shadow .2s;
    }
    .lp-plan-card:hover { box-shadow: 0 12px 40px rgba(37,99,235,.1); }
    .lp-plan-card--popular { border-color: #2563eb; }
    .lp-plan-badge {
      position: absolute; top: -13px; left: 50%; transform: translateX(-50%);
      background: #2563eb; color: #fff; font-size: .72rem; font-weight: 700;
      padding: .2rem .9rem; border-radius: 999px; white-space: nowrap;
    }
    .lp-plan-name { font-size: 1.1rem; font-weight: 700; margin: 0 0 .4rem; }
    .lp-plan-price { font-size: 2.4rem; font-weight: 900; color: #2563eb; line-height: 1; margin: .75rem 0 .25rem; letter-spacing: -.03em; }
    .lp-plan-price span { font-size: 1rem; font-weight: 500; color: #64748b; }
    .lp-plan-period { font-size: .82rem; color: #64748b; margin: 0 0 1.5rem; }
    .lp-plan-features { list-style: none; padding: 0; margin: 0 0 1.75rem; display: grid; gap: .5rem; }
    .lp-plan-features li { font-size: .88rem; color: #334155; display: flex; gap: .5rem; align-items: center; }
    .lp-plan-features li::before { content: '✓'; color: #16a34a; font-weight: 700; flex-shrink: 0; }
    .lp-plan-cta {
      display: block; text-align: center; background: #2563eb; color: #fff;
      font-weight: 700; font-size: .95rem; padding: .75rem; border-radius: 8px;
      text-decoration: none; transition: background .15s;
    }
    .lp-plan-cta:hover { background: #1d4ed8; }
    .lp-plan-cta--outline {
      background: transparent; color: #2563eb; border: 2px solid #2563eb;
    }
    .lp-plan-cta--outline:hover { background: #eff6ff; }

    /* Footer */
    .lp-footer {
      background: #0f172a; color: #94a3b8; padding: 3rem 2rem;
    }
    .lp-footer-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
    .lp-footer-brand { font-size: 1.1rem; font-weight: 800; color: #fff; }
    .lp-footer-links { display: flex; gap: 1.5rem; flex-wrap: wrap; }
    .lp-footer-links a { color: #94a3b8; text-decoration: none; font-size: .88rem; }
    .lp-footer-links a:hover { color: #fff; }

    /* Responsive */
    @media (max-width: 640px) {
      .lp-nav { padding: 0 1rem; }
      .lp-nav-links a.hide-mobile { display: none; }
      .lp-section { padding: 3.5rem 1.25rem; }
      .lp-hero { padding: 4rem 1.25rem 3rem; }
      .lp-hero-title { letter-spacing: -.01em; }
      .lp-section-title { font-size: 1.6rem; }
      .lp-section-sub { font-size: .95rem; }
      /* Pricing / feature grids: single column */
      .lp-pricing-grid { grid-template-columns: 1fr; }
      /* Footer: stack vertically */
      .lp-footer { padding: 2rem 1.25rem; }
      .lp-footer-inner { flex-direction: column; align-items: flex-start; gap: 1.25rem; }
      .lp-footer-inner > div:last-child { text-align: left !important; }
    }
  </style>
  @stack('head')
</head>
<body>

@yield('content')

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
