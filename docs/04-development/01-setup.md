# Development

This guide describes the repository workflow for contributors to `jooservices/laravel-events`.

## Command Map

```bash
composer lint          # Pint, PHPCS, PHPStan
composer lint:all      # lint + PHPMD
composer lint:fix      # Pint fix
composer test          # PHPUnit
composer test:coverage # PHPUnit coverage reports in build/coverage
composer check         # lint:all + test
composer ci            # lint:all + test:coverage
```

Prefer the `lint:*` commands in automation and docs:

```bash
composer lint:pint
composer lint:pint:fix
composer lint:phpcs
composer lint:phpstan
composer lint:phpmd
composer lint:cs
composer lint:cs:fix
```

Legacy aliases such as `composer phpstan`, `composer phpcs`, and `composer phpmd` remain for contributor convenience.

## Git Hooks

CaptainHook is installed automatically by Composer:

```bash
composer install
composer update
```

Those commands run:

```bash
captainhook install --force --skip-existing
```

The hook configuration lives in [`captainhook.json`](../../captainhook.json).

| Hook | Gate |
|------|------|
| `commit-msg` | Conventional Commit message format |
| `pre-commit` | PHP syntax linting, gitleaks staged secret scan, Pint, PHPCS, PHPStan, PHPMD |
| `pre-push` | gitleaks repository scan when available, then `composer test` |

If hooks are missing after a local environment change, reinstall them manually:

```bash
vendor/bin/captainhook install --force --skip-existing
```

The pre-commit secret scan requires the `gitleaks` binary:

```bash
brew install gitleaks
```

## Quality Tools

| Tool | Purpose |
|------|---------|
| Pint | Laravel-style formatting |
| PHPCS | Structural PHP checks |
| PHPStan / Larastan | Static analysis for Laravel package code |
| PHPMD | Maintainability and design-smell checks |
| PHP-CS-Fixer | Narrow PHPDoc cleanup that does not compete with Pint |
| PHPUnit / Testbench | Unit and Laravel package integration tests |

## CI/CD

GitHub Actions are configured for:

- Composer audit
- lint matrix for Pint, PHPCS, PHPStan, and PHPMD
- PHPUnit coverage with a MongoDB service
- non-blocking Dependency Review on pull requests
- PR labels based on changed files
- Conventional Commit-style PR title validation
- tag-driven GitHub releases and Packagist updates
- OpenSSF Scorecard analysis

The CI workflow targets PHP 8.5 and Laravel 12, matching the package's current runtime constraints.

## MongoDB Integration Tests

Storage-level tests expect a MongoDB server. CI starts `mongo:7.0` and uses:

```env
MONGODB_URI=mongodb://127.0.0.1:27017
MONGODB_DATABASE=laravel_events_test
```

Local contributors can run the same tests by starting MongoDB locally and then running:

```bash
composer test
```

## Release Process

Releases are tag-driven. Push a version tag such as:

```bash
git tag v1.0.1
git push origin v1.0.1
```

The release workflow validates the package, creates a GitHub release, and triggers a Packagist update when `PACKAGIST_USERNAME` and `PACKAGIST_TOKEN` are configured.

## Intentionally Not Copied From DTO

These DTO repository features were reviewed but not copied now:

- Codecov upload and coverage badge: not configured yet, so README does not claim it.
- hard coverage threshold: should wait until current coverage is measured and agreed for this MongoDB-backed package.
- PHPBench: not relevant to the current package surface.
- SonarCloud: optional future integration, not required for the current maturity step.

## AI Contributor Guidance

Use [AGENTS.md](../../AGENTS.md) as the canonical source for AI and human contributor rules. AI support is limited to contributor guidance and app-layer documentation; package runtime code must stay focused on Laravel events and MongoDB persistence.
