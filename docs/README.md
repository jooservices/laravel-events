# Laravel Events documentation

Documentation for `jooservices/laravel-events`, a Laravel 12 package for
MongoDB-backed event sourcing records and audit event logs.

## Reading order

1. [Architecture](00-architecture/01-project-overview.md)
2. [Getting started](01-getting-started/01-installation.md)
3. [User guide](02-user-guide/01-event-sourcing.md)
4. [Examples](03-examples/01-basic-domain-event.md)
5. [Development](04-development/01-setup.md)
6. [Maintenance](05-maintenance/01-risks-legacy-and-gaps.md)

## Architecture

- [Project overview](00-architecture/01-project-overview.md)
- [Repository structure](00-architecture/02-repository-structure.md)
- [Tech stack](00-architecture/03-tech-stack.md)
- [Package boundaries](00-architecture/04-package-boundaries.md)
- [Data flow](00-architecture/05-data-flow.md)
- [Storage model](00-architecture/06-storage-model.md)

## Getting started

- [Installation](01-getting-started/01-installation.md)
- [Configuration](01-getting-started/02-configuration.md)
- [Publish assets](01-getting-started/03-publish-assets.md)
- [First event](01-getting-started/04-first-event.md)
- [Index installation](01-getting-started/05-index-installation.md)

## User guide

- [Event sourcing](02-user-guide/01-event-sourcing.md)
- [Event log](02-user-guide/02-event-log.md)
- [Metadata, correlation, and causation](02-user-guide/03-metadata-correlation-causation.md)
- [Querying events and logs](02-user-guide/04-querying-events-and-logs.md)
- [Payload redaction](02-user-guide/05-payload-redaction.md)
- [Retention and TTL](02-user-guide/06-retention-and-ttl.md)
- [Configuration reference](02-user-guide/07-configuration-reference.md)
- [Operations](02-user-guide/08-operations.md)
- [Troubleshooting](02-user-guide/09-troubleshooting.md)
- [Best practices](02-user-guide/10-best-practices.md)
- [API reference](02-user-guide/11-api-reference.md)

## Examples

- [Basic domain event](03-examples/01-basic-domain-event.md)
- [Audit log for model change](03-examples/02-audit-log-for-model-change.md)
- [Correlation id flow](03-examples/03-correlation-id-flow.md)
- [Redaction example](03-examples/04-redaction-example.md)
- [Query history example](03-examples/05-query-history-example.md)
- [Bulk record example](03-examples/06-bulk-record-example.md)

## Development

- [Setup](04-development/01-setup.md)
- [Coding standards](04-development/02-coding-standards.md)
- [Linting standards](04-development/03-linting-standards.md)
- [Testing](04-development/04-testing.md)
- [MongoDB integration tests](04-development/05-mongodb-integration-tests.md)
- [Code quality](04-development/06-code-quality.md)
- [CI/CD](04-development/07-ci-cd.md)
- [Release process](04-development/08-release-process.md)
- [Contributing](04-development/09-contributing.md)
- [AI skills](04-development/10-ai-skills.md)
- [Secret scanning](04-development/11-secret-scanning.md)
- [Ignore files](04-development/12-ignore-files.md)
- [Optional AI integration](04-development/13-optional-ai-integration.md)

## Maintenance

- [Risks, legacy, and gaps](05-maintenance/01-risks-legacy-and-gaps.md)
- [Docs changelog](05-maintenance/02-docs-changelog.md)
- [DTO comparison audit](05-maintenance/03-repo-audit-dto-comparison.md)
