# Laravel Events — Documentation

Documentation for the **Laravel Events** package: lightweight Event Sourcing and Event Log persistence with MongoDB.

## Documentation Index

| Document | Description |
|----------|-------------|
| [Architecture](./architecture.md) | System design, data flow, and component diagrams |
| [Code Structure](./code-structure.md) | Package layout, namespaces, and file organization |
| [Installation](./installation.md) | Requirements, Composer, and Laravel setup |
| [Configuration](./configuration.md) | Config reference, environment variables, and context provider |
| [Decision Guide](./decision-guide.md) | When to use Event Sourcing, Event Log, or both |
| [Event Sourcing](./event-sourcing.md) | Storing event payloads, aggregates, and querying |
| [Event Log (Audit)](./event-log.md) | Model change audit trail, prev/changed/diff |
| [Metadata, Versioning, and Corrections](./metadata.md) | Recommended metadata keys and schema evolution conventions |
| [Operations](./operations.md) | Indexes, query patterns, retention, and production safety |
| [Optional AI Integration](./ai-integration.md) | App-layer export/manifest stubs only |
| [Development](./development.md) | Composer commands, CI/CD, release, and contributor workflow |
| [Samples](./samples.md) | Complete code samples and integration examples |
| [API Reference](./api.md) | EventService, interfaces, and console commands |

## Quick Links

- **Getting started:** [Installation](./installation.md) → [Decision Guide](./decision-guide.md)
- **Configuration:** [Configuration](./configuration.md)
- **Examples:** [Samples](./samples.md)
- **Contributing:** [Development](./development.md), [AGENTS.md](../AGENTS.md)

## Package Overview

- **EventSourcing:** Persist domain events (payload + aggregate id) to MongoDB for aggregate history, replay-aware workflows, or audit.
- **EventLog:** Persist model change events (prev/changed/diff) to MongoDB for audit trails and compliance.

Both features use Laravel's event dispatcher; you dispatch events, and the package subscribers persist them to MongoDB.

The package does not replace Laravel events, provide projections/read models, add analytics/reporting, or run AI integrations.
