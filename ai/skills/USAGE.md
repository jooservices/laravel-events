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

- Runtime/package changes: `.github/skills/laravel-events-development/SKILL.md`
- Docs and README sync: `.github/skills/documentation-sync/SKILL.md`
- CI, CaptainHook, release, and workflow changes: `.github/skills/ci-hooks-maintenance/SKILL.md`
- Security and redaction review: `.github/skills/security-hardening/SKILL.md`
- Branch and parent-target decisions: `.github/skills/git-workflow/SKILL.md`
- Final quality gate review: `.github/skills/coverage-and-lint-guard/SKILL.md`

Task-specific guidance:

- [Laravel Events implementation](laravel-events-implementation.md)
- [Laravel Events audit](laravel-events-audit.md)
