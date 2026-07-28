<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — {{ config('app.name', 'Lego Portal') }}</title>
    <link rel="icon" href="{{ asset('images/lego-logo.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('design/main.css') }}">
    @stack('styles')
</head>
<body class="page-admin">
    <a class="skip-link" href="#main-content">Skip to content</a>

    <div class="app-shell">
        @include('components.header')

        <div class="admin-shell">
            <aside class="admin-sidebar">
                <div class="actions-row" style="margin-bottom: var(--space-4);">
                    <button type="button"
                            class="btn btn-sm btn-secondary"
                            data-admin-nav-toggle
                            aria-controls="admin-nav"
                            aria-expanded="true">
                        Menu
                    </button>
                </div>
                <p class="admin-sidebar__title">Admin</p>
                <nav aria-label="Admin">
                    <ul class="admin-nav" id="admin-nav" data-admin-nav>
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                               class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.metrics.index') }}"
                               class="{{ request()->routeIs('admin.metrics.*') ? 'is-active' : '' }}">
                                Metrics
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.news.index') }}"
                               class="{{ request()->routeIs('admin.news.*') ? 'is-active' : '' }}">
                                News
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.sets.index') }}"
                               class="{{ request()->routeIs('admin.sets.*') ? 'is-active' : '' }}">
                                Sets
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.forum.index') }}"
                               class="{{ request()->routeIs('admin.forum.*') ? 'is-active' : '' }}">
                                Forum
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}"
                               class="{{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                                Users
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}">← Site</a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <div class="admin-content" id="main-content">
                @include('components.flash')
                @yield('content')
            </div>
        </div>

        @include('components.footer')
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
