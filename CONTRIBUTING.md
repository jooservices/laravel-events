# Contributing

Contributions to `jooservices/laravel-events` should keep the package aligned
with Laravel 12, PHP 8.5, MongoDB persistence, and the repository quality gates.

For more detail, see [docs/04-development/09-contributing.md](docs/04-development/09-contributing.md),
[AGENTS.md](AGENTS.md), and [CLAUDE.md](CLAUDE.md).

## Requirements

- PHP 8.5
- Composer
- MongoDB for persistence tests
- the MongoDB PHP extension

## Setup

```bash
composer install
```

## Quality gates

Use repository Composer scripts:

```bash
composer lint
composer lint:all
composer lint:fix
composer test
composer test:coverage
composer check
composer ci
```

Before commit or pull request, run the relevant checks and make sure they pass
with zero warnings or notices.

## Coding rules

- inspect the real code before changing it
- keep scope limited to Laravel event sourcing and audit event log persistence
- use SOLID, DRY, KISS, and YAGNI
- keep changes backward compatible where practical
- use real MongoDB integration flow for persisted event and audit data
- treat Pint as the source of truth when style tools disagree
- avoid unrelated refactors or cleanup outside the requested scope

## Branch workflow

- `master` is production/release state
- `develop` is development state
- feature branches start from `develop`
- hotfix branches start from `master`
- clean local work before starting new tasks

## Security

Do not report vulnerabilities in public issues. Follow [SECURITY.md](SECURITY.md)
for private reporting.
