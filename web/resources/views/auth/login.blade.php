@extends('layouts.app')

@section('title', 'Sign In')
@section('body_class', 'page-auth')

@section('content')
<div class="auth-page">
    <div class="auth-card animate-scale-pop">
        <h1>Sign In</h1>
        <p class="lead">Welcome back, builder.</p>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    required
                    autocomplete="email"
                    autofocus
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    required
                    autocomplete="current-password"
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group form-check">
                <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Remember me</label>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Sign In</button>
            </div>
        </form>

        <p style="margin-top: var(--space-6); text-align: center;">
            New here?
            <a href="{{ route('register') }}">Create an account</a>
        </p>
    </div>
</div>
@endsection
