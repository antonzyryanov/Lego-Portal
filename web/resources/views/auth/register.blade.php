@extends('layouts.app')

@section('title', 'Sign Up')
@section('body_class', 'page-auth')

@section('content')
<div class="auth-page">
    <div class="auth-card animate-scale-pop">
        <h1>Sign Up</h1>
        <p class="lead">Join Lego Portal and start posting.</p>

        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label class="form-label" for="name">Name</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    required
                    autocomplete="name"
                    autofocus
                >
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

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
                    autocomplete="new-password"
                >
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm password</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    class="form-control"
                    required
                    autocomplete="new-password"
                >
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Create account</button>
            </div>
        </form>

        <p style="margin-top: var(--space-6); text-align: center;">
            Already have an account?
            <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>
</div>
@endsection
