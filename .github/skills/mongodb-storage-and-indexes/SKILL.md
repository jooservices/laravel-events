# MongoDB Storage And Indexes

Use this skill for MongoDB persistence, index installation, retention, query behavior, and integration tests.

## What

The package writes Event Sourcing records to `stored_events` and Event Log records to `event_logs`, with connection names, collection names, and TTL behavior controlled by `config/events.php`.

## Why

MongoDB behavior is the core package responsibility. Hardcoded collection names, fake storage tests, or unverified index changes can break production retention and query paths.

## How

- Inspect `EventService`, MongoDB models, query services, `InstallIndexesCommand`, config, and integration tests before editing.
- Keep collection names, connection names, TTL seconds, and index toggles configurable.
- Do not fake MongoDB persistence in storage behavior tests.
- Use the configured MongoDB test database and clean test data between tests.
- Test index installation against real MongoDB whenever index behavior changes.
- Keep query APIs narrowly focused on stored events and audit logs; do not add analytics dashboards or tenant filtering.
- If MongoDB is unavailable, report the exact limitation instead of claiming tests passed.
- Use Laravel 12 / PHP 8.5 standards, keep Pint as formatter authority, and update docs for public storage behavior changes.
- Stop and ask when retention, TTL, index uniqueness, or migration safety is unclear.
