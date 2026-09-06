## [Unreleased]

### Changed

- Raise PHPStan to `max` with `phpstan-strict-rules` and `phpstan-phpunit`
- Enable PHPMD `cleancode` (StaticAccess / ElseExpression excluded for Laravel facades)
- Convert `EventLogAction` / `EventCategory` to string-backed enums
- Mark concrete package classes `final` (Eloquent models / service provider remain open for Testbench/Mockery); inject optional PSR-20 `ClockInterface` on `EventService`
- Introduce `EventPersisterInterface` for subscriber DIP (mockable under `final` EventService)
- CaptainHook commit subjects require an uppercase first letter
- Non-callable `context_provider` values throw `InvalidConfigurationException`
- Add Dockerfile / docker-compose / Makefile (PHP 8.5 + MongoDB) aligned with `dto`
- Index set: drop redundant prefixes; add `event_name`, sparse unique `event_id`, top-level correlation/causation indexes
- `EventSerializerInterface` requires `ensureEnvelope()`
- Migrated event record types to `jooservices/dto` (`^3.2`) and package exceptions to `jooservices/exceptions` (`^4.0`)
- Switched Pint preset from `laravel` to `per` (PER-CS 3.0)
- Removed in-repo AI/editor skill trees (workspace-owned); thinned `AGENTS.md`
- Added `SUPPORT.md`, `GOVERNANCE.md`, and `WORKFLOWS.md`
- Aligned GitHub Actions with JOOservices baseline (`ci.yml` PR gate, `ci-post-merge.yml`, commitlint, CodeQL, workflow audit)

### Fixed

- Invalid `context_provider` class-strings throw `InvalidConfigurationException` instead of a raw container error
- Stored-event `latest()` / named correlation helpers share dual-path `$or` via `QueryExecutor`
- Resolve invokable `context_provider` class-strings via the container; omit null `user_id` from EventLogSubscriber meta so context can win
- Query DTO hydration accepts Eloquent/Mongo datetime strings (and UTCDateTime) for `created_at` / `occurred_at`
- `byCorrelationId` / `byCausationId` match top-level envelope fields as well as `metadata.*`; `ensureEnvelope` copies those ids into metadata
- `StoredEventQueryService::byEventName()` now filters `event_name` (not FQCN); added `byEventClass()` and `byEventId()`
- DiffHelper records removals; deleted EventLog actions with empty `changed` store a full removal diff
- Bulk `recordManyStoredEvents()` runs `ensureEnvelope()` (always generates `event_id` / `event_name`)
- Stored-event `user_id` column now takes metadata/context `user_id` like event logs
- Query DTOs hydrate storage identity via `DocumentIdentity` (`documentId()` / `createdAt()`); `between()` rejects inverted ranges; `latest()` allowlists filters
- Legacy TTL env parsing matches retention (positive int only); default redaction keys expanded
- Runtime-truth docs: removed stale `lint:all` / legacy-namespace claims; architecture tree synced

### Added

- `InvalidEventDataException`, `InvalidQueryException`, and `InvalidConfigurationException` with structured context / error codes
- `QueryGuard` / `QueryExecutor` shared query validation and mapping
- `DateTimeParser` for Eloquent/Mongo date normalization on query hydration
- Unit coverage for `QueryExecutor::applyFilters()` dual-path OR

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

[Unreleased]: https://github.com/jooservices/laravel-events/compare/v1.5.0...HEAD
[1.5.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.5.0
[1.4.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.4.0
[1.3.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.3.0
[1.2.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.2.0
[1.1.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.1.0
[1.0.0]: https://github.com/jooservices/laravel-events/releases/tag/v1.0.0
