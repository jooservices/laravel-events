# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.3.0] - 2026-05-12

### Changed

- **Namespace policy:** Formalized `JOOservices\LaravelEvents\...` as the preferred namespace for the `1.3.x` line while keeping the legacy `JooServices\LaravelEvents\...` aliases available for compatibility.
- **Release workflow:** Removed the GitHub Discussions dependency from tag-driven releases so release publishing works in repositories where Discussions are disabled.
- **Packagist publishing:** Corrected the Packagist update payload to send the GitHub repository URL for stable tag notifications.

## [1.2.0] - 2026-05-11

### Added

- **Namespace compatibility:** `JOOservices\LaravelEvents\...` is now the canonical package namespace while the legacy `JooServices\LaravelEvents\...` namespace remains available for backward compatibility.
- **Event category support:** Added lightweight stored-event `event_category` support, including `EventMetadata::category()`, `EventMetadataBuilder::eventCategory()`, `EventSourcing\EventCategory`, top-level envelope persistence, query support, and index installation support.
- **Compatibility coverage:** Added tests to verify legacy namespace compatibility and the new event-category behavior.

### Changed

- **Batch persistence:** `EventService::recordManyStoredEvents()` and `recordManyEventLogs()` now reuse a single resolved context payload and timestamp per batch write.
- **Documentation:** Updated README and package docs for the canonical `JOOservices` namespace, the `release/<version>` flow, and stored-event category querying.
- **Dependencies:** Refreshed Composer metadata and validated the locked dependency set against the current package constraints.

### Not Added

- No package-level event taxonomy framework, projections, analytics, replay orchestration, tenant-specific policies, or AI runtime features were introduced in this release.


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

[1.3.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.3.0
[1.0.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.0.0
