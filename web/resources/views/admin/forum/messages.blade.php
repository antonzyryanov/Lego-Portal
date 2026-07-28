@extends('layouts.admin')

@section('title', 'Forum Messages')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Messages</h1>
        <p>
            Topic:
            <a href="{{ route('forum.show', $topic) }}">{{ $topic->title }}</a>
        </p>
    </div>
    <a href="{{ route('admin.forum.index') }}" class="btn btn-ghost">← Topics</a>
</header>

<div class="admin-panel reveal" style="margin-bottom: var(--space-6);">
    <h2 style="margin-top: 0; font-size: 0.7rem;">Original post</h2>
    <p class="card-meta">by {{ $topic->user->name ?? 'Unknown' }}</p>
    <div class="prose">{{ $topic->body }}</div>
</div>

<div class="stack-lg">
    @forelse ($messages ?? [] as $message)
        <article class="forum-message reveal">
            <header class="forum-message__meta">
                <span class="forum-message__author">{{ $message->user->name ?? 'Unknown' }}</span>
                <time datetime="{{ $message->created_at }}">
                    {{ \Illuminate\Support\Carbon::parse($message->created_at)->format('Y-m-d H:i') }}
                </time>
                <span class="badge badge-white">#{{ $message->id }}</span>
            </header>
            <div class="forum-message__body">{{ $message->body }}</div>
            <div class="forum-message__actions">
                <form method="POST"
                      action="{{ route('admin.forum.messages.destroy', [$topic, $message]) }}"
                      onsubmit="return confirm('Delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </article>
    @empty
        <div class="empty-state reveal">
            <h2>No replies</h2>
            <p>This topic has no messages yet.</p>
        </div>
    @endforelse
</div>

@include('components.pagination', ['paginator' => $messages ?? null])
@endsection
