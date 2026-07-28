@extends('layouts.sets')

@section('title', isset($series) && is_object($series) ? $series->name . ' Sets' : 'Sets')

@section('sets_content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">
            @if (isset($series) && is_object($series))
                {{ $series->name }}
            @elseif (!empty($series) && is_string($series))
                {{ ucwords(str_replace('-', ' ', $series)) }}
            @else
                All Sets
            @endif
        </h1>
        <p>
            @if (isset($series) && is_object($series) && $series->description)
                {{ $series->description }}
            @else
                Browse classic LEGO sets by theme. Filter with the series menu.
            @endif
        </p>
    </div>
</header>

@if (isset($sets) && count($sets))
    <div class="grid grid-3">
        @foreach ($sets as $set)
            @include('components.set-card', ['set' => $set])
        @endforeach
    </div>

    @include('components.pagination', ['paginator' => $sets])
@else
    <div class="empty-state reveal">
        <h2>No sets found</h2>
        <p>Try another series or check back after new sets are added.</p>
        <p style="margin-top: var(--space-5);">
            <a href="{{ route('sets.index') }}" class="btn btn-secondary">View all sets</a>
        </p>
    </div>
@endif
@endsection
