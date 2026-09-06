# Storage model

## stored_events

Stored event records contain:

- `event_class`
- `aggregate_id`
- `payload`
- `metadata`
- `user_id`
- `occurred_at`
- Envelope (nullable/additive): `event_id`, `event_name`, `event_category`,
  `aggregate_type`, `schema_version`, `event_version`, `correlation_id`,
  `causation_id`
- Laravel model timestamps (`created_at`, `updated_at`)

Recommended indexes are installed by `php artisan events:install-indexes`
(including sparse unique `event_id` and `event_name + created_at`).

## event_logs

Event log records contain:

- `entity_type`
- `entity_id`
- `action`
- `prev`
- `changed`
- `diff`
- `meta`
- `user_id`
- Laravel model timestamps

## Retention

Optional TTL indexes can be configured for each collection. Prefer
`retention.*_days`; legacy `eventsourcing.ttl_days` / `event_log.ttl_days` use
the same positive-integer env parsing. MongoDB performs TTL deletion
asynchronously.
