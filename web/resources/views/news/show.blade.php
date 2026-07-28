@extends('layouts.app')

@section('title', $article->title ?? 'News')

@section('content')
<div class="container section">
    <article class="admin-panel reveal" style="max-width: 48rem; margin-inline: auto;">
        <p class="card-meta" style="margin-top: 0;">
            <a href="{{ route('news.index') }}">News</a>
        </p>

        <h1 class="page-title">{{ $article->title }}</h1>

        <div class="meta-row">
            @if ($article->published_at)
                <time datetime="{{ $article->published_at }}">
                    {{ \Illuminate\Support\Carbon::parse($article->published_at)->format('F j, Y') }}
                </time>
            @endif
            @if ($article->author ?? null)
                <span class="badge">{{ $article->author->name }}</span>
            @endif
        </div>

        @if (($article->images ?? null) && count($article->images))
            <div class="news-gallery">
                @foreach ($article->images as $image)
                    <img src="{{ $image->path }}"
                         alt=""
                         loading="lazy"
                         width="240"
                         height="240">
                @endforeach
            </div>
        @endif

        <div class="prose">
            {!! nl2br(e($article->body)) !!}
        </div>
    </article>
</div>
@endsection
