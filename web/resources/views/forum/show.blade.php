@extends('layouts.app')

@section('title', $topic->title ?? 'Topic')

@section('content')
<div class="container section">
    <p class="card-meta reveal">
        <a href="{{ route('forum.index') }}">Forum</a>
    </p>

    <article class="admin-panel reveal" style="margin-bottom: var(--space-8);">
        <header class="page-header" style="margin-bottom: var(--space-5);">
            <div>
                <h1 class="page-title">{{ $topic->title }}</h1>
                <p>
                    Started by {{ $topic->user->name ?? 'Unknown' }}
                    · {{ \Illuminate\Support\Carbon::parse($topic->created_at)->format('M j, Y H:i') }}
                </p>
            </div>
            @php
                $canManageTopic = auth()->check() && (
                    auth()->id() === $topic->user_id
                    || (method_exists(auth()->user(), 'canModerateForum') && auth()->user()->canModerateForum())
                );
            @endphp
            @if ($canManageTopic)
                <div class="actions-row">
                    <a href="{{ route('forum.edit', $topic) }}" class="btn btn-sm btn-ghost">Edit</a>
                    <form method="POST"
                          action="{{ route('forum.destroy', $topic) }}"
                          onsubmit="return confirm('Delete this topic and all replies?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </div>
            @endif
        </header>

        <div class="prose">{{ $topic->body }}</div>
    </article>

    <section aria-labelledby="replies-heading">
        <h2 id="replies-heading" class="reveal" style="margin-bottom: var(--space-5);">Replies</h2>

        @php $messageList = $messages ?? ($topic->messages ?? collect()); @endphp

        @forelse ($messageList as $message)
            @include('components.forum-message', ['message' => $message, 'topic' => $topic])
        @empty
            <div class="empty-state reveal">
                <h3>No replies yet</h3>
                <p>Share your thoughts below.</p>
            </div>
        @endforelse

        @include('components.pagination', ['paginator' => $messages ?? null])
    </section>

    @auth
        <section class="admin-panel reveal" style="margin-top: var(--space-8);" aria-labelledby="reply-heading">
            <h2 id="reply-heading" style="margin-top: 0;">Post a reply</h2>
            <form method="POST" action="{{ route('forum.messages.store', $topic) }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="body">Message</label>
                    <textarea
                        id="body"
                        name="body"
                        class="form-textarea @error('body') is-invalid @enderror"
                        required
                        rows="5"
                    >{{ old('body') }}</textarea>
                    @error('body')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Post reply</button>
                </div>
            </form>
        </section>
    @else
        <div class="empty-state reveal" style="margin-top: var(--space-8);">
            <p><a href="{{ route('login') }}">Sign in</a> to reply.</p>
        </div>
    @endauth
</div>
@endsection
