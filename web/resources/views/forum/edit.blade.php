@extends('layouts.app')

@section('title', 'Edit Topic')

@section('content')
<div class="container section">
    <div class="admin-panel reveal" style="max-width: 40rem; margin-inline: auto;">
        <h1 class="page-title" style="margin-top: 0;">Edit topic</h1>

        <form method="POST" action="{{ route('forum.update', $topic) }}" style="margin-top: var(--space-6);">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="title">Title</label>
                <input
                    id="title"
                    type="text"
                    name="title"
                    value="{{ old('title', $topic->title) }}"
                    class="form-control @error('title') is-invalid @enderror"
                    required
                    maxlength="180"
                >
                @error('title')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="body">Message</label>
                <textarea
                    id="body"
                    name="body"
                    class="form-textarea @error('body') is-invalid @enderror"
                    required
                    rows="8"
                >{{ old('body', $topic->body) }}</textarea>
                @error('body')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <a href="{{ route('forum.show', $topic) }}" class="btn btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
