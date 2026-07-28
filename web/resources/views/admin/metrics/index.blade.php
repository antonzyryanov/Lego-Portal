@extends('layouts.admin')

@section('title', 'Metrics')

@section('content')
<header class="page-header reveal">
    <div>
        <h1 class="page-title">Metrics</h1>
        <p>Events collected by the metrics service.</p>
    </div>
</header>

@php
    $summary = $summary ?? [];
    $metrics = $metrics ?? [];
@endphp

@if (!empty($summary))
    <div class="stat-grid reveal">
        @foreach ($summary as $key => $value)
            @php
                if (is_array($value)) {
                    $label = $value['event_type'] ?? $value['EventType'] ?? ('item '.$key);
                    $display = $value['count'] ?? $value['Count'] ?? '—';
                } else {
                    $label = is_string($key) ? str_replace('_', ' ', ucfirst($key)) : 'Metric';
                    $display = is_numeric($value) ? number_format($value) : $value;
                }
            @endphp
            <div class="stat-tile {{ $loop->even ? 'stat-tile--red' : '' }}">
                <p class="stat-tile__label">{{ $label }}</p>
                <p class="stat-tile__value">{{ $display }}</p>
            </div>
        @endforeach
    </div>
@endif

@if (!empty($error))
    <div class="alert alert-error reveal" role="alert">{{ $error }}</div>
@endif

<div class="admin-panel reveal">
    @if (is_countable($metrics) && count($metrics))
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Event</th>
                        <th>Path</th>
                        <th>Method</th>
                        <th>IP</th>
                        <th>User</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metrics as $metric)
                        @php
                            $row = is_array($metric) ? (object) $metric : $metric;
                        @endphp
                        <tr>
                            <td>{{ $row->id ?? '—' }}</td>
                            <td><span class="badge">{{ $row->event_type ?? '—' }}</span></td>
                            <td>{{ $row->path ?? '—' }}</td>
                            <td>{{ $row->method ?? '—' }}</td>
                            <td>{{ $row->ip ?? '—' }}</td>
                            <td>{{ $row->user_id ?? '—' }}</td>
                            <td>
                                @if (!empty($row->created_at))
                                    {{ \Illuminate\Support\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @include('components.pagination', ['paginator' => $metrics])
    @else
        <div class="empty-state">
            <h2>No metrics yet</h2>
            <p>Page views will appear here once the metrics service is receiving events.</p>
        </div>
    @endif
</div>
@endsection
