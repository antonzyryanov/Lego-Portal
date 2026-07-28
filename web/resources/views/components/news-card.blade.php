@props(['article'])

<a href="{{ route('news.show', $article) }}" class="card card-link hover-lift reveal">
    <div class="card-body">
        <p class="card-meta">
            @if ($article->published_at)
                {{ \Illuminate\Support\Carbon::parse($article->published_at)->format('M j, Y') }}
            @else
                Draft
            @endif
            @if ($article->author ?? null)
                · {{ $article->author->name }}
            @endif
        </p>
        <h3 class="card-title">{{ $article->title }}</h3>
        <p class="card-excerpt">
            {{ \Illuminate\Support\Str::limit(strip_tags($article->body), 140) }}
        </p>
    </div>
</a>
