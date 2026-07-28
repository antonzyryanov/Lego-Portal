<?php

namespace App\Services\Metrics;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetricsClient
{
    /**
     * Fetch metrics events from the metrics service.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function list(array $query = []): array
    {
        return $this->get('/api/metrics', $query);
    }

    /**
     * Fetch metrics summary from the metrics service.
     *
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    public function summary(array $query = []): array
    {
        return $this->get('/api/metrics/summary', $query);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     */
    protected function get(string $path, array $query = []): array
    {
        $response = $this->client()->get($path, $query);

        if (! $response->successful()) {
            throw new RuntimeException(
                'Metrics service request failed: '.$response->status().' '.$response->body()
            );
        }

        return $response->json() ?? [];
    }

    protected function client(): PendingRequest
    {
        $baseUrl = rtrim((string) config('metrics.service_url'), '/');
        $token = (string) config('metrics.api_token');

        return Http::baseUrl($baseUrl)
            ->acceptJson()
            ->withHeaders([
                'X-Metrics-Token' => $token,
            ])
            ->timeout(10);
    }
}
