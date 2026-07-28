package queue

import (
	"context"
	"database/sql"
	"encoding/json"
	"fmt"
	"log/slog"
	"time"

	amqp "github.com/rabbitmq/amqp091-go"

	"lego-portal/metrics/internal/db"
)

const (
	queueName       = "lego.metrics"
	reconnectDelay  = 5 * time.Second
	consumePrefetch = 20
)

// IncomingEvent is the JSON payload published to the metrics queue.
type IncomingEvent struct {
	EventType string          `json:"event_type"`
	Path      string          `json:"path"`
	Method    string          `json:"method"`
	IP        string          `json:"ip"`
	UserAgent string          `json:"user_agent"`
	UserID    *int64          `json:"user_id"`
	Payload   json.RawMessage `json:"payload"`
	CreatedAt string          `json:"created_at"`
}

// Consumer reads metric events from RabbitMQ and persists them.
type Consumer struct {
	url    string
	store  *db.Store
	logger *slog.Logger
}

// NewConsumer creates a RabbitMQ consumer for the lego.metrics queue.
func NewConsumer(url string, store *db.Store, logger *slog.Logger) *Consumer {
	if logger == nil {
		logger = slog.Default()
	}
	return &Consumer{
		url:    url,
		store:  store,
		logger: logger,
	}
}

// Run connects to RabbitMQ and consumes messages until ctx is cancelled.
// It reconnects automatically on connection loss.
func (c *Consumer) Run(ctx context.Context) error {
	if c.url == "" {
		return fmt.Errorf("RABBITMQ_URL is required")
	}

	for {
		if err := ctx.Err(); err != nil {
			return err
		}

		c.logger.Info("connecting to RabbitMQ", "queue", queueName)
		if err := c.consumeOnce(ctx); err != nil {
			if ctx.Err() != nil {
				return ctx.Err()
			}
			c.logger.Error("rabbitmq consumer stopped", "error", err)
			c.logger.Info("reconnecting to RabbitMQ", "delay", reconnectDelay.String())

			select {
			case <-ctx.Done():
				return ctx.Err()
			case <-time.After(reconnectDelay):
			}
		}
	}
}

func (c *Consumer) consumeOnce(ctx context.Context) error {
	conn, err := amqp.Dial(c.url)
	if err != nil {
		return fmt.Errorf("dial rabbitmq: %w", err)
	}
	defer conn.Close()

	ch, err := conn.Channel()
	if err != nil {
		return fmt.Errorf("open channel: %w", err)
	}
	defer ch.Close()

	if err := ch.Qos(consumePrefetch, 0, false); err != nil {
		return fmt.Errorf("set qos: %w", err)
	}

	_, err = ch.QueueDeclare(
		queueName,
		true,  // durable
		false, // auto-delete
		false, // exclusive
		false, // no-wait
		nil,
	)
	if err != nil {
		return fmt.Errorf("declare queue: %w", err)
	}

	deliveries, err := ch.Consume(
		queueName,
		"",    // consumer tag
		false, // auto-ack
		false, // exclusive
		false, // no-local
		false, // no-wait
		nil,
	)
	if err != nil {
		return fmt.Errorf("start consume: %w", err)
	}

	connClosed := conn.NotifyClose(make(chan *amqp.Error, 1))
	chClosed := ch.NotifyClose(make(chan *amqp.Error, 1))

	c.logger.Info("rabbitmq consumer ready", "queue", queueName)

	for {
		select {
		case <-ctx.Done():
			return ctx.Err()
		case err := <-connClosed:
			if err != nil {
				return fmt.Errorf("connection closed: %w", err)
			}
			return fmt.Errorf("connection closed")
		case err := <-chClosed:
			if err != nil {
				return fmt.Errorf("channel closed: %w", err)
			}
			return fmt.Errorf("channel closed")
		case d, ok := <-deliveries:
			if !ok {
				return fmt.Errorf("deliveries channel closed")
			}
			if err := c.handleDelivery(ctx, d); err != nil {
				c.logger.Error("failed to process message", "error", err)
				_ = d.Nack(false, true)
				continue
			}
			if err := d.Ack(false); err != nil {
				c.logger.Error("failed to ack message", "error", err)
			}
		}
	}
}

func (c *Consumer) handleDelivery(ctx context.Context, d amqp.Delivery) error {
	var in IncomingEvent
	if err := json.Unmarshal(d.Body, &in); err != nil {
		return fmt.Errorf("unmarshal message: %w", err)
	}
	if in.EventType == "" {
		return fmt.Errorf("event_type is required")
	}

	createdAt := in.CreatedAt
	if createdAt == "" {
		createdAt = time.Now().UTC().Format(time.RFC3339Nano)
	}

	payload := "{}"
	if len(in.Payload) > 0 && string(in.Payload) != "null" {
		payload = string(in.Payload)
	}

	event := db.MetricEvent{
		EventType: in.EventType,
		Path:      in.Path,
		Method:    in.Method,
		IP:        in.IP,
		UserAgent: in.UserAgent,
		Payload:   payload,
		CreatedAt: createdAt,
	}
	if in.UserID != nil {
		event.UserID = sql.NullInt64{Int64: *in.UserID, Valid: true}
	}

	insertCtx, cancel := context.WithTimeout(ctx, 5*time.Second)
	defer cancel()

	if err := c.store.InsertEvent(insertCtx, event); err != nil {
		return err
	}

	c.logger.Debug("stored metric event",
		"event_type", event.EventType,
		"path", event.Path,
	)
	return nil
}
