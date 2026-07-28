@extends('layouts.sets')

@section('title', $set->name ?? 'Set')

@section('sets_content')
<article class="reveal">
    <p class="card-meta" style="margin-bottom: var(--space-4);">
        <a href="{{ route('sets.index') }}">Sets</a>
        @if ($set->series ?? null)
            /
            <a href="{{ route('sets.series', $set->series->slug) }}">{{ $set->series->name }}</a>
        @endif
    </p>

    <div class="detail-hero">
        <div class="detail-media animate-scale-pop">
            @if (!empty($set->image_path))
                <img src="{{ $set->image_path }}"
                     alt="{{ $set->name }}"
                     width="640"
                     height="480">
            @else
                <div style="aspect-ratio:4/3;display:grid;place-items:center;background:var(--color-surface-warm);">
                    <span class="badge">No image</span>
                </div>
            @endif
        </div>

        <div class="detail-body">
            <h1 class="page-title">{{ $set->name }}</h1>

            <div class="meta-row">
                <span class="badge">#{{ $set->article_number }}</span>
                @if ($set->series ?? null)
                    <span class="badge badge-red">{{ $set->series->name }}</span>
                @endif
                @if ($set->release_date)
                    <span class="badge badge-white">
                        {{ \Illuminate\Support\Carbon::parse($set->release_date)->format('Y') }}
                    </span>
                @endif
                @if ($set->original_price)
                    <span class="badge badge-dark">
                        ${{ number_format((float) $set->original_price, 2) }}
                    </span>
                @endif
            </div>

            <div class="prose">
                {!! nl2br(e($set->description)) !!}
            </div>
        </div>
    </div>
</article>
@endsection
