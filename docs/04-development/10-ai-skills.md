# AI skills

AI contributors must follow repository guidance in:

- [AGENTS.md](../../AGENTS.md)
- Workspace root `AGENTS.md` and `.ai/skills/` (JOOservices workspace)
- `.github/copilot-instructions.md`

Required behavior:

- inspect real code before changes
- do not assume missing requirements
- stop and report conflicts or ambiguity
- keep scope limited to Laravel event persistence and audit logging
- use PHP 8.5 and Laravel 12/13 package standards
- let Pint (`per` preset) win formatter conflicts
- use real MongoDB integration tests for persisted data
- update docs when public behavior, commands, hooks, or CI changes
- run quality gates before committing (`composer lint`, `composer test`)

This package no longer ships in-repo AI skill trees. Use the workspace
`.ai/skills/` and project docs under `docs/` instead.
