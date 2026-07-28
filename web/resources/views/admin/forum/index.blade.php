@extends('layouts.admin')

@section('title', 'Admin Forum')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Forum topics</h1>
        <p>Moderate community discussions.</p>
    </div>
</header>

<div class="admin-panel reveal">
    @if (isset($topics) && count($topics))
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($topics as $topic)
                        <tr>
                            <td>{{ $topic->title }}</td>
                            <td>{{ $topic->user->name ?? '—' }}</td>
                            <td>{{ \Illuminate\Support\Carbon::parse($topic->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <div class="actions-row">
                                    <a href="{{ route('forum.show', $topic) }}" class="btn btn-sm btn-ghost">View</a>
                                    <a href="{{ route('admin.forum.messages', $topic) }}" class="btn btn-sm btn-secondary">Messages</a>
                                    <form method="POST"
                                          action="{{ route('admin.forum.destroy', $topic) }}"
                                          onsubmit="return confirm('Delete this topic?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $topics])
    @else
        <div class="empty-state">
            <h2>No topics</h2>
            <p>Forum is quiet — nothing to moderate.</p>
        </div>
    @endif
</div>
@endsection
