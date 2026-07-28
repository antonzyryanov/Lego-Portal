@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Users</h1>
        <p>Ban accounts or promote moderators.</p>
    </div>
</header>

<div class="admin-panel reveal">
    @if (isset($users) && count($users))
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $managed)
                        @php
                            $roleSlug = $managed->role->slug ?? ($managed->role->name ?? 'user');
                            $isBanned = method_exists($managed, 'isBanned')
                                ? $managed->isBanned()
                                : (!empty($managed->banned_until) && \Illuminate\Support\Carbon::parse($managed->banned_until)->isFuture());
                            $currentUser = auth()->user();
                            $canPromote = $currentUser && method_exists($currentUser, 'isAdmin') && $currentUser->isAdmin();
                        @endphp
                        <tr>
                            <td>{{ $managed->name }}</td>
                            <td>{{ $managed->email }}</td>
                            <td>
                                <span class="badge {{ $roleSlug === 'admin' ? 'badge-red' : ($roleSlug === 'moderator' ? 'badge-dark' : 'badge-white') }}">
                                    {{ $roleSlug }}
                                </span>
                            </td>
                            <td>
                                <span class="rating-chip">★ {{ $managed->rating ?? 0 }}</span>
                            </td>
                            <td>
                                @if ($isBanned)
                                    <span class="badge badge-red">
                                        Banned until
                                        {{ \Illuminate\Support\Carbon::parse($managed->banned_until)->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="badge">Active</span>
                                @endif
                            </td>
                            <td>
                                <div class="stack">
                                    @if ($roleSlug !== 'admin')
                                        <form method="POST" action="{{ route('admin.users.ban', $managed) }}" class="form-inline">
                                            @csrf
                                            <div class="form-group" style="min-width: 7rem; flex: 0 0 auto;">
                                                <label class="form-label" for="days-{{ $managed->id }}">Ban days</label>
                                                <input
                                                    id="days-{{ $managed->id }}"
                                                    type="number"
                                                    name="days"
                                                    min="1"
                                                    max="365"
                                                    value="7"
                                                    class="form-control"
                                                    required
                                                >
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-danger">Ban</button>
                                        </form>

                                        @if ($canPromote && $roleSlug !== 'moderator')
                                            <form method="POST" action="{{ route('admin.users.promote', $managed) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-secondary">
                                                    Promote moderator
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <span class="card-meta">Protected</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $users])
    @else
        <div class="empty-state">
            <h2>No users</h2>
            <p>User accounts will appear here after registration.</p>
        </div>
    @endif
</div>
@endsection
