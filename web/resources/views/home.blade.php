@extends('layouts.app')

@section('title', 'Home')
@section('body_class', 'page-home')
@section('meta_description', 'Lego Portal — explore classic LEGO sets, read news, and join the forum.')

@section('content')
<section class="hero" aria-labelledby="hero-brand">
    <div class="hero__pattern" aria-hidden="true"></div>
    <div class="hero__accent" aria-hidden="true"></div>

    <div class="container hero__inner">
        <p id="hero-brand" class="hero__brand animate-fade-in-up">Lego Portal</p>
        <h1 class="hero__headline animate-fade-in-up reveal-delay-1" style="animation: fade-in-up var(--duration-reveal) var(--ease-out-expo) both; animation-delay: 100ms;">
            Build your collection of classic themes
        </h1>
        <p class="hero__support animate-fade-in-up" style="animation: fade-in-up var(--duration-reveal) var(--ease-out-expo) both; animation-delay: 180ms;">
            Browse Harry Potter, Star Wars, Indiana Jones, and Batman sets — then talk bricks in the forum.
        </p>
        <div class="hero__cta animate-scale-pop" style="animation-delay: 280ms;">
            <a href="{{ route('sets.index') }}" class="btn btn-primary">Explore Sets</a>
            <a href="{{ route('forum.index') }}" class="btn btn-ghost">Join Forum</a>
        </div>
    </div>
</section>
@endsection
