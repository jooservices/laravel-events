# Testing With Real MongoDB

Use this skill for PHPUnit, Orchestra Testbench, MongoDB integration tests, coverage gaps, and storage regressions.

## What

Storage behavior tests must use real MongoDB persistence through the configured test database. Unit tests can cover pure helpers, data records, serializers, and subscribers, but persistence guarantees come from integration tests.

## Why

The package's public value is durable MongoDB-backed event and audit storage. Mocked persistence can miss index, serialization, BSON, connection, timestamp, and cleanup regressions.

## How

- Inspect existing tests before adding new coverage.
- Use `tests/Integration/MongoDBIntegrationTestCase.php` for storage behavior.
- Keep integration tests isolated and clean their test collections.
- Run `composer test` for normal validation and `composer test:coverage` when coverage or CI behavior changes.
- CI uses `mongo:7.0`; local tests should use the documented `MONGODB_URI` and `MONGODB_DATABASE`.
- Report exact MongoDB availability problems instead of silently skipping.
- Do not add useless tests only to satisfy coverage. Cover behavior that could regress.
- Use Laravel 12 / PHP 8.5 package standards, keep Pint as formatter authority, and update docs when test commands or public behavior change.
- Stop and ask when a test would require fake persistence for behavior that should be proven against MongoDB.
