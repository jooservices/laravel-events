# Coverage And Lint Guard

Use this skill when validating quality gates, changing Composer scripts, coverage thresholds, or CI lint matrices.

## What

Quality gates include Composer validation, Pint, PHPCS, PHPStan/Larastan, PHPMD, PHP-CS-Fixer, PHPUnit, real MongoDB integration tests, and coverage reports. CI enforces a 95% statement coverage threshold.

## Why

The repository follows the DTO baseline for quality gates while preserving Laravel package and MongoDB-specific validation. Pint remains the formatting authority, and PHP-CS-Fixer is a secondary non-conflicting PHPDoc check.

## How

- Inspect `composer.json`, `.github/workflows/ci.yml`, `phpunit.xml`, `phpstan.neon.dist`, `phpcs.xml`, `phpmd.xml`, `.php-cs-fixer.dist.php`, and `captainhook.json` before changing gates.
- Keep `lint` as Pint + PHPCS + PHPStan.
- Keep `lint:all` as `lint` + PHPMD + PHP-CS-Fixer.
- Keep `lint:fix` as Pint fix + PHP-CS-Fixer fix.
- Use meaningful tests to protect coverage; do not game coverage by excluding source files without a valid reason.
- Run every relevant Composer gate before claiming success, and report exact failures or environment limits.
- Keep Laravel 12 / PHP 8.5 assumptions explicit.
- Use real MongoDB tests for persisted event and audit storage behavior.
- Update docs when quality gates, scripts, hooks, or CI thresholds change.
