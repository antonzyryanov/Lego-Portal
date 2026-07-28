package db

import (
	"context"
	"database/sql"
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"time"

	_ "modernc.org/sqlite"
)

const schema = `
CREATE TABLE IF NOT EXISTS metric_events (
	id         INTEGER PRIMARY KEY AUTOINCREMENT,
	event_type TEXT    NOT NULL,
	path       TEXT,
	method     TEXT,
	ip         TEXT,
	user_agent TEXT,
	user_id    INTEGER,
	payload    TEXT,
	created_at TEXT    NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_metric_events_created_at
	ON metric_events (created_at);

CREATE INDEX IF NOT EXISTS idx_metric_events_event_type
	ON metric_events (event_type);
`

// MetricEvent is a single analytics event persisted in SQLite.
type MetricEvent struct {
	ID        int64         `json:"id"`
	EventType string        `json:"event_type"`
	Path      string        `json:"path,omitempty"`
	Method    string        `json:"method,omitempty"`
	IP        string        `json:"ip,omitempty"`
	UserAgent string        `json:"user_agent,omitempty"`
	UserID    sql.NullInt64 `json:"-"`
	Payload   string        `json:"payload,omitempty"`
	CreatedAt string        `json:"created_at"`
}

// MarshalJSON renders user_id as a number or null.
func (e MetricEvent) MarshalJSON() ([]byte, error) {
	type alias struct {
		ID        int64   `json:"id"`
		EventType string  `json:"event_type"`
		Path      string  `json:"path,omitempty"`
		Method    string  `json:"method,omitempty"`
		IP        string  `json:"ip,omitempty"`
		UserAgent string  `json:"user_agent,omitempty"`
		UserID    *int64  `json:"user_id"`
		Payload   string  `json:"payload,omitempty"`
		CreatedAt string  `json:"created_at"`
	}
	out := alias{
		ID:        e.ID,
		EventType: e.EventType,
		Path:      e.Path,
		Method:    e.Method,
		IP:        e.IP,
		UserAgent: e.UserAgent,
		Payload:   e.Payload,
		CreatedAt: e.CreatedAt,
	}
	if e.UserID.Valid {
		v := e.UserID.Int64
		out.UserID = &v
	}
	return json.Marshal(out)
}

// Store wraps a SQLite connection used by the metrics service.
type Store struct {
	db *sql.DB
}

// Open opens (or creates) the SQLite database at dbPath and runs migrations.
func Open(dbPath string) (*Store, error) {
	if err := os.MkdirAll(filepath.Dir(dbPath), 0o755); err != nil {
		return nil, fmt.Errorf("create db directory: %w", err)
	}

	dsn := fmt.Sprintf("file:%s?_pragma=busy_timeout(5000)&_pragma=journal_mode(WAL)&_pragma=foreign_keys(1)", dbPath)
	sqlDB, err := sql.Open("sqlite", dsn)
	if err != nil {
		return nil, fmt.Errorf("open sqlite: %w", err)
	}

	sqlDB.SetMaxOpenConns(1)
	sqlDB.SetConnMaxLifetime(0)
	sqlDB.SetConnMaxIdleTime(0)

	ctx, cancel := context.WithTimeout(context.Background(), 10*time.Second)
	defer cancel()

	if err := sqlDB.PingContext(ctx); err != nil {
		_ = sqlDB.Close()
		return nil, fmt.Errorf("ping sqlite: %w", err)
	}

	if _, err := sqlDB.ExecContext(ctx, schema); err != nil {
		_ = sqlDB.Close()
		return nil, fmt.Errorf("migrate schema: %w", err)
	}

	return &Store{db: sqlDB}, nil
}

// Close closes the underlying database connection.
func (s *Store) Close() error {
	if s == nil || s.db == nil {
		return nil
	}
	return s.db.Close()
}

// InsertEvent persists a metric event.
func (s *Store) InsertEvent(ctx context.Context, e MetricEvent) error {
	const q = `
INSERT INTO metric_events (
	event_type, path, method, ip, user_agent, user_id, payload, created_at
) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
`
	var userID any
	if e.UserID.Valid {
		userID = e.UserID.Int64
	}

	_, err := s.db.ExecContext(ctx, q,
		e.EventType,
		nullIfEmpty(e.Path),
		nullIfEmpty(e.Method),
		nullIfEmpty(e.IP),
		nullIfEmpty(e.UserAgent),
		userID,
		nullIfEmpty(e.Payload),
		e.CreatedAt,
	)
	if err != nil {
		return fmt.Errorf("insert metric event: %w", err)
	}
	return nil
}

// ListFilter holds optional filters for listing metric events.
type ListFilter struct {
	From      string
	To        string
	EventType string
	Limit     int
}

// ListEvents returns metric events matching the given filters, newest first.
func (s *Store) ListEvents(ctx context.Context, f ListFilter) ([]MetricEvent, error) {
	if f.Limit <= 0 {
		f.Limit = 100
	}
	if f.Limit > 1000 {
		f.Limit = 1000
	}

	q := `
SELECT id, event_type, path, method, ip, user_agent, user_id, payload, created_at
FROM metric_events
WHERE 1=1
`
	args := make([]any, 0, 4)

	if f.From != "" {
		q += ` AND created_at >= ?`
		args = append(args, f.From)
	}
	if f.To != "" {
		q += ` AND created_at <= ?`
		args = append(args, f.To)
	}
	if f.EventType != "" {
		q += ` AND event_type = ?`
		args = append(args, f.EventType)
	}

	q += ` ORDER BY created_at DESC, id DESC LIMIT ?`
	args = append(args, f.Limit)

	rows, err := s.db.QueryContext(ctx, q, args...)
	if err != nil {
		return nil, fmt.Errorf("list metric events: %w", err)
	}
	defer rows.Close()

	events := make([]MetricEvent, 0)
	for rows.Next() {
		var e MetricEvent
		var path, method, ip, ua, payload sql.NullString
		if err := rows.Scan(
			&e.ID,
			&e.EventType,
			&path,
			&method,
			&ip,
			&ua,
			&e.UserID,
			&payload,
			&e.CreatedAt,
		); err != nil {
			return nil, fmt.Errorf("scan metric event: %w", err)
		}
		e.Path = path.String
		e.Method = method.String
		e.IP = ip.String
		e.UserAgent = ua.String
		e.Payload = payload.String
		events = append(events, e)
	}
	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("iterate metric events: %w", err)
	}
	return events, nil
}

// EventCount is an aggregated count for a single event_type.
type EventCount struct {
	EventType string `json:"event_type"`
	Count     int64  `json:"count"`
}

// Summary returns counts grouped by event_type for the optional time range.
func (s *Store) Summary(ctx context.Context, from, to string) ([]EventCount, error) {
	q := `
SELECT event_type, COUNT(*) AS cnt
FROM metric_events
WHERE 1=1
`
	args := make([]any, 0, 2)

	if from != "" {
		q += ` AND created_at >= ?`
		args = append(args, from)
	}
	if to != "" {
		q += ` AND created_at <= ?`
		args = append(args, to)
	}

	q += ` GROUP BY event_type ORDER BY cnt DESC, event_type ASC`

	rows, err := s.db.QueryContext(ctx, q, args...)
	if err != nil {
		return nil, fmt.Errorf("summary metric events: %w", err)
	}
	defer rows.Close()

	out := make([]EventCount, 0)
	for rows.Next() {
		var c EventCount
		if err := rows.Scan(&c.EventType, &c.Count); err != nil {
			return nil, fmt.Errorf("scan summary row: %w", err)
		}
		out = append(out, c)
	}
	if err := rows.Err(); err != nil {
		return nil, fmt.Errorf("iterate summary rows: %w", err)
	}
	return out, nil
}

func nullIfEmpty(s string) any {
	if s == "" {
		return nil
	}
	return s
}
