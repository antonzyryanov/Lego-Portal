@props(['message', 'topic' => null])

@php
    $user = auth()->user();
    $canManage = $user && (
        $user->id === $message->user_id
        || (method_exists($user, 'canModerateForum') && $user->canModerateForum())
    );
    $topicParam = $topic ?? $message->topic_id;
    $avatar = $message->user->avatar
        ?? asset('images/default-avatar.svg');
@endphp

<article class="forum-message reveal" id="message-{{ $message->id }}">
    <header class="forum-message__meta">
        <img
            src="{{ $avatar }}"
            alt=""
            class="forum-message__avatar"
            width="40"
            height="40"
            loading="lazy"
        >
        <span class="forum-message__author">
            {{ $message->user->name ?? 'Unknown' }}
        </span>
        @if (($message->user->rating ?? null) !== null)
            <span class="rating-chip">★ {{ $message->user->rating }}</span>
        @endif
        <time datetime="{{ $message->created_at }}">
            {{ \Illuminate\Support\Carbon::parse($message->created_at)->format('M j, Y H:i') }}
        </time>
    </header>

    <div class="forum-message__body">{{ $message->body }}</div>

    @if ($canManage)
        <div class="forum-message__actions">
            <form method="POST"
                  action="{{ route('forum.messages.destroy', [$topicParam, $message]) }}"
                  onsubmit="return confirm('Delete this message?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    @endif
</article>
