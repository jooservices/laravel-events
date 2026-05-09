# AI skill usage

Shared baseline:

- `AGENTS.md`
- `CLAUDE.md`
- `ai/skills/README.md`
- `.github/skills/`

- Inspect real code before changes.
- Do not assume.
- Stop and report ambiguity or conflicts.
- Keep scope limited to Laravel event sourcing and MongoDB audit event logs.
- Use PHP 8.5 and Laravel 12.
- Use real MongoDB integration tests for persisted data.
- Pint wins formatter conflicts.
- Run Composer quality gates before commit.

Recommended task routing:

- Repository quality and branch safety: `.github/skills/repo-quality-foundation/SKILL.md`
- Laravel package wiring: `.github/skills/laravel-package-development/SKILL.md`
- Architecture decisions: `.github/skills/architecture-and-design-principles/SKILL.md`
- Event Sourcing changes: `.github/skills/event-sourcing-boundaries/SKILL.md`
- Event Log audit changes: `.github/skills/event-log-audit-boundaries/SKILL.md`
- MongoDB storage and indexes: `.github/skills/mongodb-storage-and-indexes/SKILL.md`
- DTO-style records and serializers: `.github/skills/dto-style-records/SKILL.md`
- Redaction and sensitive data: `.github/skills/redaction-and-sensitive-data/SKILL.md`
- Testing with real MongoDB: `.github/skills/testing-with-real-mongodb/SKILL.md`
- Coverage and lint gates: `.github/skills/coverage-and-lint-guard/SKILL.md`
- Docs and README sync: `.github/skills/documentation-sync/SKILL.md`
- CI, CaptainHook, release, and workflow changes: `.github/skills/ci-hooks-maintenance/SKILL.md`
- Release management: `.github/skills/release-management/SKILL.md`
- Security hardening review: `.github/skills/security-hardening/SKILL.md`
- Branch and parent-target decisions: `.github/skills/git-workflow/SKILL.md`
- Legacy package adapter: `.github/skills/laravel-events-development/SKILL.md`

Task-specific guidance:

- [Laravel Events implementation](laravel-events-implementation.md)
- [Laravel Events audit](laravel-events-audit.md)
