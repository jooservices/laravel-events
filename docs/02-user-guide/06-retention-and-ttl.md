# Retention and TTL

Configure TTL retention with:

```env
EVENTS_EVENTSOURCING_TTL_DAYS=
EVENTS_EVENT_LOG_TTL_DAYS=365
EVENTS_STORED_EVENTS_RETENTION_DAYS=
EVENTS_EVENT_LOGS_RETENTION_DAYS=365
```

`null` or an empty value means no TTL index is created. A positive integer
creates a TTL index during:

```bash
php artisan events:install-indexes
```

MongoDB TTL deletion is asynchronous. Expired records may remain for a short
time after their configured age.

Avoid TTL on event-sourced records that may be required for replay, legal
retention, or historical reconstruction.
