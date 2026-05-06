# Configuration reference

See [getting started configuration](../01-getting-started/02-configuration.md)
for the complete current configuration table.

The package reads configuration from `config/events.php`:

- `connection`
- `context_provider`
- `eventsourcing.enabled`
- `eventsourcing.collection`
- `eventsourcing.ttl_days`
- `event_log.enabled`
- `event_log.collection`
- `event_log.ttl_days`

Additional redaction and retention keys may be added as scoped package features
are implemented.
