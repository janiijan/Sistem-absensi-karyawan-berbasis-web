<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title') · Sepa Indonesia</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body>
  <div class="overlay"></div>
  <div class="app-shell">
    @include('partials.sidebar')
    <main class="main">
      @if (session('success'))
        <div class="alert alert-success show" style="margin-bottom:16px;">{{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="alert alert-error show" style="margin-bottom:16px;">{{ session('error') }}</div>
      @endif
      @yield('content')
    </main>
  </div>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
