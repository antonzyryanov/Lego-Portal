<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Metrics\MetricsClient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class MetricsController extends Controller
{
    public function index(Request $request, MetricsClient $client): View
    {
        $filters = $request->only(['from', 'to', 'event_type', 'limit']);
        $summary = [];
        $events = [];
        $error = null;

        try {
            $summary = $client->summary(array_filter([
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
            ]));
            $events = $client->list(array_filter([
                'from' => $filters['from'] ?? null,
                'to' => $filters['to'] ?? null,
                'event_type' => $filters['event_type'] ?? null,
                'limit' => $filters['limit'] ?? 100,
            ]));
        } catch (Throwable $e) {
            $error = 'Unable to load metrics: '.$e->getMessage();
        }

        return view('admin.metrics.index', [
            'summary' => $summary['summary'] ?? $summary,
            'metrics' => $events['events'] ?? $events,
            'filters' => $filters,
            'error' => $error,
        ]);
    }
}
