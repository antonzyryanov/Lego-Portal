@extends('layouts.admin')

@section('title', 'Admin Sets')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Sets</h1>
        <p>Manage the LEGO set catalog.</p>
    </div>
    @if (!auth()->user() || !method_exists(auth()->user(), 'canCreateContent') || auth()->user()->canCreateContent())
        <a href="{{ route('admin.sets.create') }}" class="btn btn-primary">New set</a>
    @endif
</header>

<form method="GET" action="{{ route('admin.sets.index') }}" class="filters-bar reveal">
    <div class="form-group">
        <label class="form-label" for="series_id">Series</label>
        <select id="series_id" name="series_id" class="form-select">
            <option value="">All series</option>
            @foreach ($seriesList ?? [] as $item)
                <option value="{{ $item->id }}" @selected((string) request('series_id') === (string) $item->id)>
                    {{ $item->name }}
                </option>
            @endforeach
        </select>
    </div>
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
        <input id="q" type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or article #">
    </div>
    <button type="submit" class="btn btn-secondary">Filter</button>
    <a href="{{ route('admin.sets.index') }}" class="btn btn-ghost">Reset</a>
</form>

<div class="admin-panel reveal">
    @if (isset($sets) && count($sets))
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Article</th>
                        <th>Name</th>
                        <th>Series</th>
                        <th>Released</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sets as $set)
                        <tr>
                            <td>{{ $set->article_number }}</td>
                            <td>{{ $set->name }}</td>
                            <td>{{ $set->series->name ?? '—' }}</td>
                            <td>
                                @if ($set->release_date)
                                    {{ \Illuminate\Support\Carbon::parse($set->release_date)->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if ($set->original_price)
                                    ${{ number_format((float) $set->original_price, 2) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="actions-row">
                                    <a href="{{ route('sets.show', $set) }}" class="btn btn-sm btn-ghost">View</a>
                                    @if (!method_exists(auth()->user(), 'canManageContent') || auth()->user()->canManageContent())
                                        <a href="{{ route('admin.sets.edit', $set) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form method="POST"
                                              action="{{ route('admin.sets.destroy', $set) }}"
                                              onsubmit="return confirm('Delete this set?');">
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

        @include('components.pagination', ['paginator' => $sets])
    @else
        <div class="empty-state">
            <h2>No sets found</h2>
            <p>Adjust filters or add a new set to the catalog.</p>
            <p style="margin-top: var(--space-5);">
                <a href="{{ route('admin.sets.create') }}" class="btn btn-primary">New set</a>
            </p>
        </div>
    @endif
</div>
@endsection
