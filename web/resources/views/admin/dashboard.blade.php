@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p>Quick overview of Lego Portal activity.</p>
    </div>
</header>

@php
    $summary = $summary ?? [];
@endphp

<div class="stat-grid reveal">
    <div class="stat-tile">
        <p class="stat-tile__label">Users</p>
        <p class="stat-tile__value">{{ $summary['users'] ?? ($usersCount ?? '—') }}</p>
    </div>
    <div class="stat-tile stat-tile--red">
        <p class="stat-tile__label">Sets</p>
        <p class="stat-tile__value">{{ $summary['sets'] ?? ($setsCount ?? '—') }}</p>
    </div>
    <div class="stat-tile">
        <p class="stat-tile__label">News</p>
        <p class="stat-tile__value">{{ $summary['news'] ?? ($newsCount ?? '—') }}</p>
    </div>
    <div class="stat-tile stat-tile--red">
        <p class="stat-tile__label">Topics</p>
        <p class="stat-tile__value">{{ $summary['topics'] ?? ($topicsCount ?? '—') }}</p>
    </div>
</div>

<div class="admin-panel reveal">
    <h2 style="margin-top: 0;">Shortcuts</h2>
    <div class="actions-row">
        <a href="{{ route('admin.metrics.index') }}" class="btn btn-secondary">Metrics</a>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">New article</a>
        <a href="{{ route('admin.sets.create') }}" class="btn btn-primary">New set</a>
        <a href="{{ route('admin.forum.index') }}" class="btn btn-ghost">Moderate forum</a>
        <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Users</a>
    </div>
</div>
@endsection
