# Laravel Events — Documentation

Enterprise-grade documentation for the **Laravel Events** package: EventSourcing and EventLog with MongoDB persistence.

## Documentation Index

| Document | Description |
|----------|-------------|
| [Architecture](./architecture.md) | System design, data flow, and component diagrams |
| [Code Structure](./code-structure.md) | Package layout, namespaces, and file organization |
| [Installation](./installation.md) | Requirements, Composer, and Laravel setup |
| [Configuration](./configuration.md) | Config reference, environment variables, and context provider |
| [Event Sourcing](./event-sourcing.md) | Storing event payloads, aggregates, and querying |
| [Event Log (Audit)](./event-log.md) | Model change audit trail, prev/changed/diff |
| [Samples](./samples.md) | Complete code samples and integration examples |
| [API Reference](./api.md) | EventService, interfaces, and console commands |

## Quick Links

- **Getting started:** [Installation](./installation.md) → [Event Sourcing](./event-sourcing.md) or [Event Log](./event-log.md)
- **Configuration:** [Configuration](./configuration.md)
- **Examples:** [Samples](./samples.md)

## Package Overview

- **EventSourcing:** Persist domain events (payload + aggregate id) to MongoDB for replay, analytics, or audit.
- **EventLog:** Persist model change events (prev/changed/diff) to MongoDB for audit trails and compliance.

Both features use Laravel's event dispatcher; you dispatch events, and the package subscribers persist them to MongoDB.
