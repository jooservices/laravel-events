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
