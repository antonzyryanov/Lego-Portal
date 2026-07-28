@extends('layouts.app')

@section('title', 'Forum')

@section('content')
<div class="container section">
    <header class="page-header reveal">
        <div>
            <h1 class="page-title">Forum</h1>
            <p>Discuss sets, trades, and building tips. Posting earns rating points.</p>
        </div>
        @auth
            <a href="{{ route('forum.create') }}" class="btn btn-primary">New topic</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-secondary">Sign in to post</a>
        @endauth
    </header>

    @if (isset($topics) && count($topics))
        <ul class="topic-list">
            @foreach ($topics as $topic)
                <li>
                    <a href="{{ route('forum.show', $topic) }}" class="topic-item reveal hover-lift">
                        <h2>{{ $topic->title }}</h2>
                        <p>
                            by {{ $topic->user->name ?? 'Unknown' }}
                            · {{ \Illuminate\Support\Carbon::parse($topic->created_at)->diffForHumans() }}
                            @if (isset($topic->messages_count))
                                · {{ $topic->messages_count }} replies
                            @elseif (($topic->messages ?? null))
                                · {{ $topic->messages->count() }} replies
                            @endif
                        </p>
                        <p style="margin-top: var(--space-2);">
                            {{ \Illuminate\Support\Str::limit(strip_tags($topic->body), 120) }}
                        </p>
                    </a>
                </li>
            @endforeach
        </ul>

        @include('components.pagination', ['paginator' => $topics])
    @else
        <div class="empty-state reveal">
            <h2>No topics yet</h2>
            <p>Be the first to start a conversation.</p>
            @auth
                <p style="margin-top: var(--space-5);">
                    <a href="{{ route('forum.create') }}" class="btn btn-primary">Start a topic</a>
                </p>
            @endauth
        </div>
    @endif
</div>
@endsection
