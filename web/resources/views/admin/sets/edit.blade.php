@extends('layouts.admin')

@section('title', 'Edit Set')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Edit set</h1>
        <p>{{ $set->name }}</p>
    </div>
</header>

<div class="admin-panel reveal" style="max-width: 44rem;">
    <form method="POST" action="{{ route('admin.sets.update', $set) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label" for="series_id">Series</label>
            <select
                id="series_id"
                name="series_id"
                class="form-select @error('series_id') is-invalid @enderror"
                required
            >
                @foreach ($seriesList ?? [] as $item)
                    <option value="{{ $item->id }}" @selected((string) old('series_id', $set->series_id) === (string) $item->id)>
                        {{ $item->name }}
                    </option>
                @endforeach
            </select>
            @error('series_id')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="name">Name</label>
            <input
                id="name"
                type="text"
                name="name"
                value="{{ old('name', $set->name) }}"
                class="form-control @error('name') is-invalid @enderror"
                required
            >
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="article_number">Article number</label>
            <input
                id="article_number"
                type="text"
                name="article_number"
                value="{{ old('article_number', $set->article_number) }}"
                class="form-control @error('article_number') is-invalid @enderror"
                required
            >
            @error('article_number')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea
                id="description"
                name="description"
                class="form-textarea @error('description') is-invalid @enderror"
                required
                rows="6"
            >{{ old('description', $set->description) }}</textarea>
            @error('description')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-inline" style="margin-bottom: var(--space-5);">
            <div class="form-group">
                <label class="form-label" for="original_price">Original price</label>
                <input
                    id="original_price"
                    type="number"
                    step="0.01"
                    min="0"
                    name="original_price"
                    value="{{ old('original_price', $set->original_price) }}"
                    class="form-control @error('original_price') is-invalid @enderror"
                    required
                >
                @error('original_price')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label" for="release_date">Release date</label>
                <input
                    id="release_date"
                    type="date"
                    name="release_date"
                    value="{{ old('release_date', $set->release_date ? \Illuminate\Support\Carbon::parse($set->release_date)->format('Y-m-d') : '') }}"
                    class="form-control @error('release_date') is-invalid @enderror"
                    required
                >
                @error('release_date')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        @if (!empty($set->image_path))
            <div class="form-group">
                <span class="form-label">Current image</span>
                <div class="detail-media" style="max-width: 16rem;">
                    <img src="{{ $set->image_path }}" alt="{{ $set->name }}" width="320" height="240">
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label" for="image_path">Image URL</label>
            <input
                id="image_path"
                type="url"
                name="image_path"
                value="{{ old('image_path', $set->image_path) }}"
                class="form-control @error('image_path') is-invalid @enderror"
            >
            @error('image_path')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="image">Or upload image</label>
            <input
                id="image"
                type="file"
                name="image"
                class="form-control @error('image') is-invalid @enderror"
                accept="image/*"
            >
            @error('image')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="{{ route('admin.sets.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
