# CI Hooks Maintenance

Use this skill for Composer scripts, CaptainHook, GitHub Actions, dependency,
secret-scanning, release, and quality gate changes.

Required behavior:

- inspect DTO baseline and the live repository before changing tooling
- Pint remains the formatting authority
- PHP-CS-Fixer is limited to narrow PHPDoc cleanup that does not fight Pint
- keep `composer validate --strict`, `composer audit`, Pint, PHPCS, PHPStan/Larastan, PHPMD, PHP-CS-Fixer, PHPUnit, and coverage generation wired through CI where practical
- keep MongoDB services available for integration tests in CI
- do not add blocking external services unless required secrets and repo settings are known
- run `composer validate --strict`, `composer lint:all`, and `composer test` after tooling changes
- keep Laravel 12 / PHP 8.5 assumptions explicit in workflows and docs
- update README/docs when commands, hooks, or CI behavior change
- stop and ask when tooling, secrets, branch state, or validation output conflicts
