@props(['set'])

<a href="{{ route('sets.show', $set) }}" class="card card-link hover-lift reveal">
    <div class="card-media">
        @if (!empty($set->image_path))
            <img src="{{ $set->image_path }}"
                 alt="{{ $set->name }}"
                 loading="lazy"
                 width="400"
                 height="300">
        @endif
    </div>
    <div class="card-body">
        <h3 class="card-title">{{ $set->name }}</h3>
        <p class="card-meta">
            #{{ $set->article_number }}
            @if ($set->series ?? null)
                · {{ $set->series->name }}
            @endif
        </p>
        <p class="card-meta">
            @if ($set->release_date)
                {{ \Illuminate\Support\Carbon::parse($set->release_date)->format('Y') }}
            @endif
            @if ($set->original_price)
                · ${{ number_format((float) $set->original_price, 2) }}
            @endif
        </p>
    </div>
</a>
