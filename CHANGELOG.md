# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

- **Carbon:** Event time (`occurredAt`) and StoredEvent `occurred_at` use Carbon (`\Carbon\CarbonInterface`) instead of `\DateTimeInterface`.
- **HasEventSourcingDefaults** trait: default `occurredAt(): null` and `metadata(): []` so events need only implement `payload()` and `aggregateId()`.
- **DefaultsToUpdatedAction** trait: default `getAction(): 'updated'` for `HasLogAction` so log events need not implement `getAction()` when action is always updated.
- **EventLogAction** constants for the recommended event log action taxonomy (`created`, `updated`, `deleted`, `restored`, `status_changed`, `corrected`, `synchronized`, `imported`).
- **EventMetadata** helper with constants and small factory methods for trace, source, version, tenant, and correction metadata conventions.
- **Documentation:** Added decision guide, metadata/versioning/correction conventions, operations guidance, production safety notes, and optional AI integration stubs.

### Changed

- `EventService::storeEvent()` now accepts `?\Carbon\CarbonInterface $occurredAt` (was `?\DateTimeInterface`).
- **Documentation:** Updated `./docs` (api, code-structure, configuration, event-log, event-sourcing, installation, samples).
- **Indexes:** `events:install-indexes` now also creates safe compound indexes for common chronological aggregate, event class, entity, and action queries.

### Not Added

- No AI runtime, agents, data fetching, projections/read models, analytics/reporting, compensation engine, or upcaster framework.

## [1.0.0] - 2026-03-09

### Added

- **Event Sourcing:** Persist domain events (payload + aggregate id) to MongoDB collection `stored_events`.
  - `EventSourcingInterface` with `payload()` and `aggregateId()`.
  - Optional `occurredAt()` and `metadata()` on events (Carbon).
  - `EventSourcingSubscriber` listening for `EventSourcingInterface`.
- **Event Log (Audit):** Persist model change events (prev/changed/diff) to MongoDB collection `event_logs`.
  - `LoggableModelInterface` with `getLoggableType()`, `getLoggableId()`, `getPrev()`, `getChanged()`.
  - Optional `HasLogAction` for action (created/updated/deleted/restored).
  - `EventLogSubscriber` and `DiffHelper` for per-field diff.
- **EventService:** Singleton service for `storeEvent()` and `logChange()`, with optional context provider merge.
- **Configuration:** `config/events.php` for connection, collections, TTL, and context provider.
- **Console:** `events:install-indexes` command to create or drop MongoDB indexes and optional TTL.
- **Documentation:** Enterprise docs in `./docs` (architecture, code structure, installation, configuration, event sourcing, event log, samples, API reference).
- **Quality:** Laravel Pint, PHPStan (Larastan), PHPMD, PHPCS; PHP 8.5, Laravel 12, PHPUnit 12, PHP_CodeSniffer 4.

### Requirements

- PHP ^8.5
- Laravel ^12.0
- mongodb/laravel-mongodb ^5.6

[1.0.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.0.0
