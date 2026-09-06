# Changelog

All notable changes to this package are documented in this file.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/);
versioning follows [Semantic Versioning](https://semver.org/).

## [Unreleased]

## [4.0.0] - 2026-09-06

Major quality and correctness release for Laravel 12/13 + MongoDB event
persistence. Treat upgrades from `1.x` as a breaking change — see
[`UPGRADE-4.0.md`](UPGRADE-4.0.md).

### Breaking

- `EventLogAction` and `EventCategory` are string-backed enums. Call sites that
  compared or returned `EventLogAction::UPDATED` as a string must use
  `EventLogAction::UPDATED->value` (and the same pattern for other cases).
  `::all()` still returns `list<string>`.
- Concrete package classes are `final` (`EventService`, subscribers, query
  services, serializer, DiffHelper, console command, etc.). Eloquent models and
  `EventsServiceProvider` remain open. Prefer composition / `EventPersisterInterface`
  over subclassing.
- Removed unused bag DTOs `EventDiffData` and `EventMetadataData`. Record DTOs
  keep `diff` / `meta` / `metadata` as arrays.
- `EventSerializerInterface` requires `ensureEnvelope()`.
- Mongo index set changed (sparse unique `event_id`, `event_name`, top-level
  correlation/causation). Re-run `php artisan events:install-indexes` after
  upgrade.
- Runtime dependencies now include `jooservices/dto` `^3.2`,
  `jooservices/exceptions` `^4.0`, `psr/clock` `^1.0`, and `psr/log` `^3.0`.

### Upgrade

1. `composer require jooservices/laravel-events:^4.0`
2. Update action/category call sites to `->value` where a `string` is required.
3. Publish config if you customize it; re-run `php artisan events:install-indexes`.
4. Prefer `ShouldDispatchAfterCommit` for domain events that must not outlive a
   rolled-back SQL transaction; embed `user_id` / correlation on queued events.
5. Type-hint `EventPersisterInterface` in tests instead of mocking `final`
   `EventService` directly.

### Added

- `EventPersisterInterface` for subscriber DIP
- Optional PSR-20 `ClockInterface` and PSR-3 `LoggerInterface` on `EventService`
- `InvalidEventDataException`, `InvalidQueryException`,
  `InvalidConfigurationException`
- `QueryGuard` / `QueryExecutor`, `DateTimeParser`, `DocumentIdentity`
- Dockerfile / docker-compose / Makefile (PHP 8.5 + MongoDB)
- Dual-path correlation queries; invokable `context_provider` class-strings

### Changed

- PHPStan `max` + `phpstan-strict-rules` / `phpstan-phpunit`; PHPMD cleancode
- Pint `per` (PER-CS 3.0); CaptainHook uppercase Conventional Commit subjects
- JOOservices CI baseline (`ci.yml` PR gate, commitlint, CodeQL, workflow audit)
- Non-callable `context_provider` throws `InvalidConfigurationException`

### Fixed

- Query DTO date hydration from Eloquent/Mongo strings
- `byEventName()` filters `event_name`; added `byEventClass()` / `byEventId()`
- Diff removals; deleted EventLog empty `changed` → full removal diff
- Bulk `recordManyStoredEvents()` always runs `ensureEnvelope()`
- Guest EventLog does not overwrite context `user_id` with null
- Allowlisted query filters; inverted `between()` rejected

## [1.5.0] - 2026-07-26

### Changed

- Removed alternate-casing PSR-4 compatibility. The only public PHP root
  namespace is `JOOservices\LaravelEvents` (uppercase `OO`).

## [1.4.0] - 2026-06-25

### Added

- Added Laravel 13 support alongside Laravel 12: `laravel/framework` now accepts `^12.0|^13.0`
- Added `orchestra/testbench:^11.0` to `require-dev` so the package can be tested against Laravel 13
- Added a CI test matrix running the suite against both Laravel 12 and Laravel 13

### Changed

- Bumped the `mongodb/laravel-mongodb` floor to `^5.7` (the first release with Laravel 13 support)
- Updated docs, AI skills, and agent instructions to state the Laravel 12/13 support range

## [1.3.0] - 2026-05-12

### Changed

- **Namespace policy:** Formalized `JOOservices\LaravelEvents\...` as the public namespace for the `1.3.x` line.
- **Release workflow:** Removed the GitHub Discussions dependency from tag-driven releases so release publishing works in repositories where Discussions are disabled.
- **Packagist publishing:** Corrected the Packagist update payload to send the GitHub repository URL for stable tag notifications.

## [1.2.0] - 2026-05-11

### Added

- **Namespace policy:** Documented `JOOservices\LaravelEvents\...` as the canonical package namespace.
- **Event category support:** Added lightweight stored-event `event_category` support, including `EventMetadata::category()`, `EventMetadataBuilder::eventCategory()`, `EventSourcing\EventCategory`, top-level envelope persistence, query support, and index installation support.
- **Namespace transition coverage:** Added tests for the package namespace transition and the new event-category behavior.

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

[Unreleased]: https://github.com/jooservices/laravel-events/compare/v4.0.0...HEAD
[4.0.0]: https://github.com/jooservices/laravel-events/releases/tag/v4.0.0
[1.5.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.5.0
[1.4.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.4.0
[1.3.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.3.0
[1.2.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.2.0
[1.1.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.1.0
[1.0.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.0.0
