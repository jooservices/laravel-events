# Troubleshooting

## No records are persisted

- Confirm the event implements `EventSourcingInterface` or
  `LoggableModelInterface`.
- Confirm `events.eventsourcing.enabled` or `events.event_log.enabled` is true.
- Confirm the package service provider is discovered or registered.
- Confirm the configured MongoDB connection works.

## Index command fails

- Confirm `config('events.connection')` points to a MongoDB connection.
- Confirm the MongoDB PHP extension is installed.
- Confirm the MongoDB server is reachable.

## Metadata is missing

- Confirm `context_provider` is callable.
- Confirm it returns an array.
- Event-specific metadata is merged after context metadata and can override
  matching keys.

## Tests cannot reach MongoDB

Set `MONGODB_URI` and `MONGODB_DATABASE`, or start a local MongoDB service
before running integration tests.
