<?php

namespace App\Services\Metrics;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class MetricsPublisher
{
    /**
     * Publish a JSON payload to the metrics RabbitMQ queue.
     * Failures are swallowed so the web app stays available when MQ is down.
     *
     * @param  array<string, mixed>  $payload
     */
    public function publish(array $payload): void
    {
        try {
            $config = config('metrics.rabbitmq');

            $connection = new AMQPStreamConnection(
                $config['host'],
                (int) $config['port'],
                $config['user'],
                $config['password'],
                $config['vhost'],
            );

            $channel = $connection->channel();
            $queue = $config['queue'];

            $channel->queue_declare($queue, false, true, false, false);

            $message = new AMQPMessage(
                json_encode($payload, JSON_THROW_ON_ERROR),
                [
                    'content_type' => 'application/json',
                    'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
                ],
            );

            $channel->basic_publish($message, '', $queue);

            $channel->close();
            $connection->close();
        } catch (Throwable) {
            // Fail silently.
        }
    }
}
