# Configuration reference

See [getting started configuration](../01-getting-started/02-configuration.md)
for the complete current configuration table.

The package reads configuration from `config/events.php`:

- `connection`
- `context_provider`
- `redaction.enabled`
- `redaction.keys`
- `redaction.replacement`
- `retention.stored_events_days`
- `retention.event_logs_days`
- `eventsourcing.enabled`
- `eventsourcing.collection`
- `eventsourcing.ttl_days`
- `event_log.enabled`
- `event_log.collection`
- `event_log.ttl_days`

Legacy `eventsourcing.ttl_days` and `event_log.ttl_days` remain supported, but
the top-level `retention` keys are preferred.
