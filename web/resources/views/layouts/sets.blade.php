@extends('layouts.app')

@section('body_class', 'page-sets')

@section('content')
<div class="container sets-layout">
    <aside class="sets-sidebar" data-sets-sidebar id="sets-sidebar">
        <h2 class="sets-sidebar__title">Series</h2>
        <ul class="sets-sidebar__nav">
            <li>
                <a href="{{ route('sets.index') }}"
                   class="{{ !isset($series) || blank($series) ? 'is-active' : '' }}">
                    All sets
                </a>
            </li>
            @php
                $defaultSeries = [
                    ['name' => 'Harry Potter', 'slug' => 'harry-potter'],
                    ['name' => 'Star Wars', 'slug' => 'star-wars'],
                    ['name' => 'Indiana Jones', 'slug' => 'indiana-jones'],
                    ['name' => 'Batman', 'slug' => 'batman'],
                ];
                $menuSeries = isset($seriesList) && count($seriesList) ? $seriesList : $defaultSeries;
            @endphp
            @foreach ($menuSeries as $item)
                @php
                    $slug = is_object($item) ? $item->slug : ($item['slug'] ?? '');
                    $name = is_object($item) ? $item->name : ($item['name'] ?? '');
                    $activeSlug = is_object($series ?? null) ? $series->slug : ($series ?? request()->route('series'));
                @endphp
                <li>
                    <a href="{{ route('sets.series', $slug) }}"
                       class="{{ $activeSlug === $slug ? 'is-active' : '' }}">
                        {{ $name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </aside>

    <div class="sets-content">
        <div class="actions-row" style="margin-bottom: var(--space-4);">
            <button type="button"
                    class="sidebar-toggle"
                    data-sidebar-toggle
                    aria-controls="sets-sidebar"
                    aria-expanded="false"
                    aria-label="Toggle series menu">
                <span></span>
            </button>
            <span class="sr-only">Series filter</span>
        </div>

        @yield('sets_content')
    </div>
</div>
@endsection
