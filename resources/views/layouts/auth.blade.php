<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'TouchBase') — TouchBase CRM</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-body">

<div class="auth-wrap">
  <div class="auth-brand">
    <span style="font-size:1.5rem;">&#9679;</span> TouchBase
  </div>

  <div class="auth-card">
    @if(session('success'))
      <div class="alert alert-success" style="margin-bottom:1rem;">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger" style="margin-bottom:1rem;">&#9888; {{ session('error') }}</div>
    @endif

    @yield('content')
  </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
