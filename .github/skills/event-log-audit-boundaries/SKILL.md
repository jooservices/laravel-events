# Event Log Audit Boundaries

Use this skill when changing `LoggableModelInterface`, event log entries, model audit subscribers, diff behavior, or audit-log docs.

## What

Event Log records are field-level audit records persisted to the configured `event_logs` MongoDB collection. They focus on entity identity, action names, `prev`, `changed`, `diff`, metadata, and timestamps.

## Why

Audit trails need consistent before/after semantics without becoming business analytics, authorization, tenant filtering, or complete model snapshot storage. Keeping the boundary small prevents hidden application policy in the package.

## How

- Inspect current subscriber, model, data record, diff helper, redaction, and tests before changing audit behavior.
- Keep recommended action names in `EventLogAction`; do not hardcode application-specific catalogs.
- Store only the fields needed for audit diffs. Do not add complete snapshots unless explicitly approved and documented.
- Preserve explicit null changes and changed-only semantics.
- Apply redaction before persisting sensitive fields.
- Use real MongoDB integration tests for storage behavior.
- Update README and docs when action, diff, metadata, or retention behavior changes.
- Use Laravel 12 / PHP 8.5 standards and let Pint win formatter conflicts.
- Run Composer quality gates before claiming audit behavior is done.
