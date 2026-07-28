@php
    $user = auth()->user();
    $isStaff = $user && (method_exists($user, 'isAdmin')
        ? ($user->isAdmin() || $user->isModerator())
        : false);
@endphp

<header class="site-header" data-site-header>
    <div class="site-header__inner">
        <a href="{{ route('home') }}" class="brand-link" aria-label="Lego Portal home">
            <img src="{{ asset('images/lego-logo.svg') }}" alt="" class="brand-logo" width="40" height="40">
            <span class="brand-text">Lego Portal</span>
        </a>

        <button type="button"
                class="nav-toggle"
                data-nav-toggle
                aria-controls="site-nav"
                aria-expanded="false"
                aria-label="Open menu">
            <span></span>
        </button>

        <nav class="site-nav" id="site-nav" data-site-nav aria-label="Primary">
            <ul class="site-nav__links">
                <li>
                    <a href="{{ route('sets.index') }}"
                       class="{{ request()->routeIs('sets.*') ? 'is-active' : '' }}">
                        Sets
                    </a>
                </li>
                <li>
                    <a href="{{ route('news.index') }}"
                       class="{{ request()->routeIs('news.*') ? 'is-active' : '' }}">
                        News
                    </a>
                </li>
                <li>
                    <a href="{{ route('forum.index') }}"
                       class="{{ request()->routeIs('forum.*') ? 'is-active' : '' }}">
                        Forum
                    </a>
                </li>
                @if ($isStaff)
                    <li>
                        <a href="{{ route('admin.dashboard') }}"
                           class="{{ request()->routeIs('admin.*') ? 'is-active' : '' }}">
                            Admin
                        </a>
                    </li>
                @endif
            </ul>

            <div class="site-nav__user">
                @auth
                    <div class="user-menu">
                        <span class="user-name">{{ $user->name }}</span>
                        <span class="rating-chip" title="Forum rating">
                            ★ {{ $user->rating ?? 0 }}
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ghost">Sign out</button>
                        </form>
                    </div>
                @else
                    <div class="user-menu">
                        <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Sign In</a>
                        <a href="{{ route('register') }}" class="btn btn-sm btn-primary">Sign Up</a>
                    </div>
                @endauth
            </div>
        </nav>
    </div>
</header>
