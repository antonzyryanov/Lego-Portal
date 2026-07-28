@extends('layouts.admin')

@section('title', 'Create News')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">New article</h1>
        <p>Write and publish portal news.</p>
    </div>
</header>

<div class="admin-panel reveal" style="max-width: 44rem;">
    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="form-group">
            <label class="form-label" for="title">Title</label>
            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title') }}"
                class="form-control @error('title') is-invalid @enderror"
                required
            >
            @error('title')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="body">Body</label>
            <textarea
                id="body"
                name="body"
                class="form-textarea @error('body') is-invalid @enderror"
                required
                rows="12"
            >{{ old('body') }}</textarea>
            @error('body')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="published_at">Published at</label>
            <input
                id="published_at"
                type="datetime-local"
                name="published_at"
                value="{{ old('published_at') }}"
                class="form-control @error('published_at') is-invalid @enderror"
            >
            <span class="form-hint">Leave empty to save as draft.</span>
            @error('published_at')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="images">Images</label>
            <input
                id="images"
                type="file"
                name="images[]"
                class="form-control @error('images') is-invalid @enderror"
                accept="image/*"
                multiple
            >
            @error('images')
                <span class="form-error">{{ $message }}</span>
            @enderror
            @error('images.*')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create article</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
