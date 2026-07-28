<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lego Portal') — {{ config('app.name', 'Lego Portal') }}</title>
    <meta name="description" content="@yield('meta_description', 'Lego Portal — classic sets, news, and community forum.')">
    <link rel="icon" href="{{ asset('images/lego-logo.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('design/main.css') }}">
    @stack('styles')
</head>
<body class="@yield('body_class')">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="app-shell">
        @include('components.header')

        <main id="main-content" class="app-main">
            <div class="container section-sm">
                @include('components.flash')
            </div>

            @yield('content')
        </main>

        @include('components.footer')
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
