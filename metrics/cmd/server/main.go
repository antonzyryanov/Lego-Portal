package main

import (
	"context"
	"errors"
	"log/slog"
	"net/http"
	"os"
	"os/signal"
	"syscall"
	"time"

	"lego-portal/metrics/internal/db"
	"lego-portal/metrics/internal/httpapi"
	"lego-portal/metrics/internal/queue"
)

func main() {
	logger := slog.New(slog.NewJSONHandler(os.Stdout, &slog.HandlerOptions{
		Level: slog.LevelInfo,
	}))

	if err := run(logger); err != nil {
		logger.Error("fatal", "error", err)
		os.Exit(1)
	}
}

func run(logger *slog.Logger) error {
	cfg := loadConfig()

	store, err := db.Open(cfg.dbPath)
	if err != nil {
		return err
	}
	defer store.Close()

	ctx, stop := signal.NotifyContext(context.Background(), os.Interrupt, syscall.SIGTERM)
	defer stop()

	consumer := queue.NewConsumer(cfg.rabbitURL, store, logger)
	go func() {
		if err := consumer.Run(ctx); err != nil && !errors.Is(err, context.Canceled) {
			logger.Error("consumer exited", "error", err)
			stop()
		}
	}()

	api := httpapi.New(store, cfg.apiToken, logger)
	srv := &http.Server{
		Addr:              ":" + cfg.port,
		Handler:           api.Handler(),
		ReadHeaderTimeout: 5 * time.Second,
		ReadTimeout:       15 * time.Second,
		WriteTimeout:      30 * time.Second,
		IdleTimeout:       60 * time.Second,
	}

	errCh := make(chan error, 1)
	go func() {
		logger.Info("http server listening", "addr", srv.Addr)
		if err := srv.ListenAndServe(); err != nil && !errors.Is(err, http.ErrServerClosed) {
			errCh <- err
		}
		close(errCh)
	}()

	select {
	case <-ctx.Done():
		logger.Info("shutting down")
	case err := <-errCh:
		if err != nil {
			return err
		}
	}

	shutdownCtx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()
	if err := srv.Shutdown(shutdownCtx); err != nil {
		return err
	}
	return nil
}

type config struct {
	rabbitURL string
	dbPath    string
	apiToken  string
	port      string
}

func loadConfig() config {
	return config{
		rabbitURL: envOr("RABBITMQ_URL", ""),
		dbPath:    envOr("METRICS_DB_PATH", "/data/metrics.db"),
		apiToken:  envOr("METRICS_API_TOKEN", ""),
		port:      envOr("PORT", "8081"),
	}
}

func envOr(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}
