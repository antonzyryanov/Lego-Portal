<?php

return [

    'service_url' => env('METRICS_SERVICE_URL', 'http://metrics'),

    'api_token' => env('METRICS_API_TOKEN'),

    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', '127.0.0.1'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'queue' => env('RABBITMQ_QUEUE', 'lego.metrics'),
        'url' => env('RABBITMQ_URL'),
    ],

];
