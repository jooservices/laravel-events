# Claude Code Instructions For `jooservices/laravel-events`

Read [AGENTS.md](./AGENTS.md) first.

When working in this repository:

- Prefer the smallest change that fits the existing Laravel package structure.
- Inspect the real codebase before changing anything and stop on ambiguity or conflicts.
- Use PHP 8.5 and Laravel 12 package standards.
- Keep Event Sourcing and Event Log concepts separate.
- Match Laravel conventions and the current namespace layout.
- Do not introduce projections, analytics, query frameworks, or AI runtime behavior into the package core.
- Keep MongoDB persistence configurable through `config/events.php`.
- Use real MongoDB integration flow for persisted event and audit data; do not fake storage records.
- Pint wins formatting conflicts.
- Keep tests and docs in sync with behavior changes.
- Use the repository command map from `composer.json`, especially `composer lint`, `composer lint:all`, `composer test`, `composer check`, and `composer ci`.
- Keep `master` for production, `develop` for development, feature branches from `develop`, normal pull requests to `develop`, and hotfix branches/pull requests from `master`.
- Stop and ask before changing files if branch state, requirements, docs, CI, runtime behavior, or package scope conflict.
- Treat work as complete only when the relevant validation commands pass or an environment limitation is reported exactly.
- Commit completed feature groups as `Viet Vu <jooservices@gmail.com>`.
- Be explicit about compatibility impact, metadata handling, and operational risk.
