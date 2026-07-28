@extends('layouts.app')

@section('title', 'News')

@section('content')
<div class="container section">
    <header class="page-header reveal">
        <div>
            <h1 class="page-title">News</h1>
            <p>Portal updates, set highlights, and community stories.</p>
        </div>
    </header>

    @if (isset($news) && count($news))
        <div class="grid grid-2">
            @foreach ($news as $article)
                @include('components.news-card', ['article' => $article])
            @endforeach
        </div>

        @include('components.pagination', ['paginator' => $news])
    @else
        <div class="empty-state reveal">
            <h2>No news yet</h2>
            <p>Check back soon for the latest from Lego Portal.</p>
        </div>
    @endif
</div>
@endsection
