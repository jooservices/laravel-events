# Repository quality instructions

- Inspect the real current codebase before changing anything.
- Do not assume missing requirements.
- Stop and report conflicts between requirements, code, docs, CI, or repository state.
- Use PHP 8.5 and Laravel 12 package standards.
- Keep scope tight: Laravel-native event sourcing and audit event log persistence for MongoDB.
- Do not add dashboards, analytics UI, projection framework, AI runtime, or unrelated app features.
- Use real MongoDB integration flow for persisted event and audit data.
- Do not fake persisted records.
- Pint is the primary formatter and wins conflicts.
- Use `composer ci` as the canonical full local gate.
- Keep `master` and `develop` aligned before repository quality passes.
- Use feature branches from `develop` and hotfix branches from `master`.
- Commit completed feature groups with author `Viet Vu <jooservices@gmail.com>`.
