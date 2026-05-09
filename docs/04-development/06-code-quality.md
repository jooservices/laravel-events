# Code quality

Before committing a completed feature group, run the relevant focused tests and
quality commands. Before final handoff, run:

```bash
composer validate --strict
composer lint
composer lint:all
composer test
composer test:coverage
composer check
composer ci
```

Fix warnings, risky tests, skipped failures, and ignored lint issues before
claiming success.

Current repository baseline:

- PHPStan level 7 through `phpstan.neon.dist`
- PHPUnit strict warning, notice, deprecation, risky-test, and output handling
- 95% minimum statement coverage enforced by CI
