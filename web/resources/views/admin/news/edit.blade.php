@extends('layouts.admin')

@section('title', 'Edit News')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Edit article</h1>
        <p>{{ $article->title }}</p>
    </div>
</header>

<div class="admin-panel reveal" style="max-width: 44rem;">
    <form method="POST" action="{{ route('admin.news.update', $article) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label" for="title">Title</label>
            <input
                id="title"
                type="text"
                name="title"
                value="{{ old('title', $article->title) }}"
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
            >{{ old('body', $article->body) }}</textarea>
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
                value="{{ old('published_at', $article->published_at ? \Illuminate\Support\Carbon::parse($article->published_at)->format('Y-m-d\TH:i') : '') }}"
                class="form-control @error('published_at') is-invalid @enderror"
            >
            <span class="form-hint">Leave empty to keep as draft.</span>
            @error('published_at')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        @if (($article->images ?? null) && count($article->images))
            <div class="form-group">
                <span class="form-label">Current images</span>
                <div class="news-gallery">
                    @foreach ($article->images as $image)
                        <img src="{{ $image->path }}" alt="" width="160" height="160">
                    @endforeach
                </div>
            </div>
        @endif

        <div class="form-group">
            <label class="form-label" for="images">Add images</label>
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
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save changes</button>
            <a href="{{ route('admin.news.index') }}" class="btn btn-ghost">Cancel</a>
        </div>
    </form>
</div>
@endsection
