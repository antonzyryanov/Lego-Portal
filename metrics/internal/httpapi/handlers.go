package httpapi

import (
	"context"
	"encoding/json"
	"log/slog"
	"net/http"
	"strconv"
	"strings"
	"time"

	"lego-portal/metrics/internal/db"
)

const headerMetricsToken = "X-Metrics-Token"

// Server exposes the metrics HTTP API.
type Server struct {
	store     *db.Store
	apiToken  string
	logger    *slog.Logger
	readyFunc func() bool
}

// New creates an HTTP API server backed by store.
func New(store *db.Store, apiToken string, logger *slog.Logger) *Server {
	if logger == nil {
		logger = slog.Default()
	}
	return &Server{
		store:    store,
		apiToken: apiToken,
		logger:   logger,
		readyFunc: func() bool {
			return store != nil
		},
	}
}

// Handler returns the root HTTP handler with all routes registered.
func (s *Server) Handler() http.Handler {
	mux := http.NewServeMux()
	mux.HandleFunc("GET /health", s.handleHealth)
	mux.HandleFunc("GET /api/metrics", s.requireToken(s.handleListMetrics))
	mux.HandleFunc("GET /api/metrics/summary", s.requireToken(s.handleSummary))
	return withLogging(s.logger, mux)
}

func (s *Server) handleHealth(w http.ResponseWriter, r *http.Request) {
	status := http.StatusOK
	body := map[string]any{
		"status": "ok",
		"time":   time.Now().UTC().Format(time.RFC3339),
	}
	if s.readyFunc != nil && !s.readyFunc() {
		status = http.StatusServiceUnavailable
		body["status"] = "unavailable"
	}
	writeJSON(w, status, body)
}

func (s *Server) handleListMetrics(w http.ResponseWriter, r *http.Request) {
	q := r.URL.Query()
	limit := 100
	if raw := q.Get("limit"); raw != "" {
		n, err := strconv.Atoi(raw)
		if err != nil || n < 1 {
			writeError(w, http.StatusBadRequest, "invalid limit")
			return
		}
		limit = n
	}

	filter := db.ListFilter{
		From:      strings.TrimSpace(q.Get("from")),
		To:        strings.TrimSpace(q.Get("to")),
		EventType: strings.TrimSpace(q.Get("event_type")),
		Limit:     limit,
	}

	ctx, cancel := context.WithTimeout(r.Context(), 10*time.Second)
	defer cancel()

	events, err := s.store.ListEvents(ctx, filter)
	if err != nil {
		s.logger.Error("list metrics failed", "error", err)
		writeError(w, http.StatusInternalServerError, "failed to list metrics")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{
		"events": events,
		"count":  len(events),
	})
}

func (s *Server) handleSummary(w http.ResponseWriter, r *http.Request) {
	q := r.URL.Query()
	from := strings.TrimSpace(q.Get("from"))
	to := strings.TrimSpace(q.Get("to"))

	ctx, cancel := context.WithTimeout(r.Context(), 10*time.Second)
	defer cancel()

	summary, err := s.store.Summary(ctx, from, to)
	if err != nil {
		s.logger.Error("summary failed", "error", err)
		writeError(w, http.StatusInternalServerError, "failed to build summary")
		return
	}

	writeJSON(w, http.StatusOK, map[string]any{
		"summary": summary,
	})
}

func (s *Server) requireToken(next http.HandlerFunc) http.HandlerFunc {
	return func(w http.ResponseWriter, r *http.Request) {
		if s.apiToken == "" {
			s.logger.Error("METRICS_API_TOKEN is not configured")
			writeError(w, http.StatusServiceUnavailable, "api token not configured")
			return
		}
		token := strings.TrimSpace(r.Header.Get(headerMetricsToken))
		if token == "" || token != s.apiToken {
			writeError(w, http.StatusUnauthorized, "unauthorized")
			return
		}
		next(w, r)
	}
}

func writeJSON(w http.ResponseWriter, status int, v any) {
	w.Header().Set("Content-Type", "application/json; charset=utf-8")
	w.WriteHeader(status)
	enc := json.NewEncoder(w)
	enc.SetEscapeHTML(true)
	_ = enc.Encode(v)
}

func writeError(w http.ResponseWriter, status int, msg string) {
	writeJSON(w, status, map[string]string{"error": msg})
}

type statusWriter struct {
	http.ResponseWriter
	code int
}

func (w *statusWriter) WriteHeader(code int) {
	w.code = code
	w.ResponseWriter.WriteHeader(code)
}

func withLogging(logger *slog.Logger, next http.Handler) http.Handler {
	return http.HandlerFunc(func(w http.ResponseWriter, r *http.Request) {
		start := time.Now()
		sw := &statusWriter{ResponseWriter: w, code: http.StatusOK}
		next.ServeHTTP(sw, r)
		logger.Info("http request",
			"method", r.Method,
			"path", r.URL.Path,
			"status", sw.code,
			"duration_ms", time.Since(start).Milliseconds(),
			"remote", r.RemoteAddr,
		)
	})
}
