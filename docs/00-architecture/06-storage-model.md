# Storage model

## stored_events

Stored event records contain:

- `event_class`
- `aggregate_id`
- `payload`
- `metadata`
- `user_id`
- `occurred_at`
- Laravel model timestamps

Recommended indexes are installed by `php artisan events:install-indexes`.

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

Optional TTL indexes can be configured for each collection. MongoDB performs TTL
deletion asynchronously, so expired records are not deleted immediately at the
exact configured age.
