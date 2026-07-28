@extends('layouts.admin')

@section('title', 'Admin News')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">News</h1>
        <p>Manage portal articles.</p>
    </div>
    @if (!auth()->user() || !method_exists(auth()->user(), 'canCreateContent') || auth()->user()->canCreateContent())
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">New article</a>
    @endif
</header>

<form method="GET" action="{{ route('admin.news.index') }}" class="filters-bar reveal">
    <div class="form-group">
        <label class="form-label" for="from">From</label>
        <input id="from" type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>
    <div class="form-group">
        <label class="form-label" for="to">To</label>
        <input id="to" type="date" name="to" value="{{ request('to') }}" class="form-control">
    </div>
    <div class="form-group">
        <label class="form-label" for="q">Search</label>
        <input id="q" type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Title…">
    </div>
    <button type="submit" class="btn btn-secondary">Filter</button>
    <a href="{{ route('admin.news.index') }}" class="btn btn-ghost">Reset</a>
</form>

<div class="admin-panel reveal">
    @if (isset($news) && count($news))
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($news as $article)
                        <tr>
                            <td>{{ $article->title }}</td>
                            <td>{{ $article->author->name ?? '—' }}</td>
                            <td>
                                @if ($article->published_at)
                                    {{ \Illuminate\Support\Carbon::parse($article->published_at)->format('Y-m-d') }}
                                @else
                                    <span class="badge badge-white">Draft</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-row">
                                    <a href="{{ route('news.show', $article) }}" class="btn btn-sm btn-ghost">View</a>
                                    @if (!method_exists(auth()->user(), 'canManageContent') || auth()->user()->canManageContent())
                                        <a href="{{ route('admin.news.edit', $article) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form method="POST"
                                              action="{{ route('admin.news.destroy', $article) }}"
                                              onsubmit="return confirm('Delete this article?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $news])
    @else
        <div class="empty-state">
            <h2>No articles</h2>
            <p>Create the first news post for the portal.</p>
            <p style="margin-top: var(--space-5);">
                <a href="{{ route('admin.news.create') }}" class="btn btn-primary">New article</a>
            </p>
        </div>
    @endif
</div>
@endsection
