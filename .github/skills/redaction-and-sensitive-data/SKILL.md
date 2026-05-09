# Redaction And Sensitive Data

Use this skill when changing payload redaction, metadata handling, logging examples, docs, or AI/app-layer data guidance.

## What

The package supports configurable payload redaction and metadata conventions. It must never encourage storing secrets, raw tokens, private keys, or unnecessary PII in event payloads or metadata.

## Why

Event streams and audit logs can have long retention. Sensitive data persisted there is hard to remove later and may be exposed to analytics, debugging, backups, or optional app-layer AI workflows.

## How

- Inspect `PayloadRedactor`, config redaction keys, stored event paths, event log paths, docs, and tests before editing.
- Redact before persistence where the package owns the persistence path.
- Keep event-specific metadata application-owned and flexible.
- Prefer stable metadata keys such as `request_id`, `correlation_id`, `causation_id`, `source`, `channel`, `reason_code`, `schema_version`, `event_version`, and `tenant_id`.
- Do not add AI data export, external AI tools, or agent runtime code to the package.
- If documenting app-layer AI use, require authorization, redaction, retention controls, and audit controls.
- Add focused tests for nested redaction and explicit null behavior when relevant.
- Use Laravel 12 / PHP 8.5 standards, keep Pint as formatter authority, and test storage with real MongoDB when persistence behavior changes.
- Stop and ask when sensitivity, authorization, retention, or compliance expectations are unclear.
