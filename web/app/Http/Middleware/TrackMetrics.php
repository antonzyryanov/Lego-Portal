<?php

namespace App\Http\Middleware;

use App\Services\Metrics\MetricsPublisher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackMetrics
{
    public function __construct(
        private readonly MetricsPublisher $publisher,
    ) {}

    /**
     * Publish a page_view metric event (fails silently if RabbitMQ is down).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            if ($request->isMethod('GET') && ! $request->is('up')) {
                $this->publisher->publish([
                    'event_type' => 'page_view',
                    'path' => '/'.$request->path(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => (string) $request->userAgent(),
                    'user_id' => $request->user()?->id,
                    'payload' => [
                        'status' => $response->getStatusCode(),
                    ],
                    'created_at' => now()->utc()->toIso8601String(),
                ]);
            }
        } catch (Throwable) {
            // Fail silently when metrics infrastructure is unavailable.
        }

        return $response;
    }
}
