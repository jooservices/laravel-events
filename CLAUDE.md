# Claude Code Instructions For `jooservices/laravel-events`

Read [AGENTS.md](./AGENTS.md) first.

When working in this repository:

- Prefer the smallest change that fits the existing Laravel package structure.
- Keep Event Sourcing and Event Log concepts separate.
- Match Laravel conventions and the current namespace layout.
- Do not introduce projections, analytics, query frameworks, or AI runtime behavior into the package core.
- Keep MongoDB persistence configurable through `config/events.php`.
- Keep tests and docs in sync with behavior changes.
- Use the repository command map from `composer.json`, especially `composer lint`, `composer lint:all`, `composer test`, and `composer check`.
- Be explicit about compatibility impact, metadata handling, and operational risk.
