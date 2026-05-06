# Laravel Events Repository Instructions

This repository is the PHP package `jooservices/laravel-events`.

## Core Intent

- Preserve Laravel-native event dispatching; do not replace it with a custom bus.
- Keep the package focused on MongoDB persistence for Event Sourcing records and Event Log audit records.
- Prefer small, additive, compatibility-safe changes that fit the existing package structure.
- Treat docs and tests as part of the implementation whenever public behavior changes.

## Package Boundaries

The package provides:

- `EventSourcingInterface` records stored in the `stored_events` MongoDB collection.
- `LoggableModelInterface` records stored in the `event_logs` MongoDB collection.
- `EventService` as the persistence service.
- Laravel subscribers, config publishing, and the `events:install-indexes` Artisan command.
- Metadata conventions through `EventMetadata`.
- Recommended event-log action names through `EventLogAction`.

The package does not provide:

- a projection or read-model framework
- replay orchestration
- business analytics or dashboards
- authorization, redaction, or tenant filtering for consumers
- AI agents, AI data fetching, or an AI runtime

## Event Sourcing Versus Event Log

- Use Event Sourcing for domain events that should remain meaningful by aggregate over time.
- Use Event Log for field-level model/entity audit trails with `prev`, `changed`, and `diff`.
- Keep both record types focused. Do not duplicate complete model snapshots into domain events unless replay explicitly requires them.

## Repository Quality Rules

- Inspect the real current codebase before changing anything.
- Do not assume missing requirements or repository state.
- Stop and report conflicts between requirements, code, docs, CI, or branch state.
- Use PHP 8.5 and latest Laravel 12 package standards.
- Formatting authority: Laravel Pint.
- Structural checks: PHPCS.
- Static analysis: PHPStan through Larastan.
- Maintainability checks: PHPMD.
- Tests: PHPUnit with Orchestra Testbench.
- Commit completed feature groups with author `Viet Vu <jooservices@gmail.com>`.

## Command Map

- `composer lint`
- `composer lint:all`
- `composer lint:fix`
- `composer test`
- `composer test:coverage`
- `composer check`
- `composer ci`

Prefer `lint:*` scripts in automation and documentation. Legacy aliases such as `composer phpstan` exist only for contributor convenience.

## Git Hooks

- CaptainHook is installed automatically by Composer on `post-install-cmd` and `post-update-cmd`.
- Hook configuration lives in `captainhook.json`.
- `commit-msg` enforces Conventional Commits.
- `pre-commit` runs PHP syntax linting, gitleaks staged secret scanning, Pint, PHPCS, PHPStan, and PHPMD.
- `pre-push` runs a gitleaks repository scan when available and then `composer test`.
- Contributors need the `gitleaks` binary installed locally for the blocking pre-commit secret scan.

## Laravel And MongoDB Rules

- Keep Laravel service provider discovery in `composer.json`.
- Keep MongoDB collection names, connection names, and TTL behavior configurable through `config/events.php`.
- Do not hardcode application model names, tenant rules, auth policies, or event catalogs inside the package.
- Integration tests that touch storage must use the configured MongoDB test database whenever possible.
- Do not fake persisted event or audit data in storage behavior tests.

## Metadata Rules

- Metadata is flexible, but contributors should prefer stable keys such as `request_id`, `correlation_id`, `causation_id`, `source`, `channel`, `reason_code`, `schema_version`, `event_version`, and `tenant_id`.
- Event-specific metadata should remain application-owned.
- Never store secrets, raw tokens, private keys, or unnecessary PII in payloads or metadata.

## AI Contributor Guidance

- AI support in this repository means contributor guidance and app-layer documentation only.
- Do not add agent runtime code, external AI tool execution, or built-in AI data export to the package.
- If documenting AI use, keep it framed as application responsibility with explicit authorization, redaction, retention, and audit controls.

## Branch Workflow

- `master` is production/release state.
- `develop` is development state.
- Feature branches start from `develop`.
- Hotfix branches start from `master`.
- Clean local work before starting new tasks.
- Do not leave successful completed work uncommitted.

## Change Checklist

Before considering a task done:

1. Keep the change minimal and package-appropriate.
2. Preserve backward compatibility unless a breaking change is explicitly requested.
3. Add or update tests for changed behavior.
4. Update README/docs when commands, workflows, or public behavior change.
5. Run the relevant Composer quality commands.
6. Explain risk, tradeoff, and compatibility impact in the final summary.
