# Architecture And Design Principles

Use this skill when changing package structure, public contracts, service responsibilities, or cross-cutting behavior in `jooservices/laravel-events`.

## What

Keep the package a Laravel 12 / PHP 8.5 event persistence package. The architecture centers on Laravel-native event dispatching, `EventService`, MongoDB-backed stored events, MongoDB-backed audit logs, typed data records, query services, metadata helpers, and index installation.

## Why

Applications should be able to adopt the package without accepting a custom event bus, projection framework, dashboard, tenant policy, authorization layer, replay runtime, or AI runtime. Those concerns belong in the consuming application unless they are explicitly requested as small opt-in package features.

## How

- Inspect the current code, config, docs, and tests before changing architecture.
- Keep Event Sourcing records and Event Log records conceptually separate.
- Prefer additive, backward-compatible changes to public interfaces, config keys, MongoDB field names, and data records.
- Apply SOLID, DRY, KISS, and YAGNI without adding abstractions before the package has a concrete need.
- Keep MongoDB collection names, connection names, TTL settings, redaction, and context behavior configurable through `config/events.php`.
- Do not guess around unclear branch state, CI expectations, public behavior, or storage semantics; stop and ask.
- Update docs and tests with behavior changes.
- Run Composer quality gates before done. Pint wins formatter conflicts, and storage tests must use real MongoDB persistence.
