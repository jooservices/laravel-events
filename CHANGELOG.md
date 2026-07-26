## [Unreleased]

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
