# CI/CD

CI runs on `master`, `develop`, and pull requests targeting those branches.

Current jobs include:

- Composer audit
- Composer metadata validation
- Pint, PHPCS, PHPStan/Larastan, PHPMD, and narrow PHP-CS-Fixer PHPDoc checks
- PHPUnit coverage with a MongoDB service
- coverage artifact upload (minimum threshold: 95%)
- dependency review for pull requests
- optional Codecov upload when `CODECOV_TOKEN` is configured
- optional SonarQube Cloud analysis when `SONAR_TOKEN` is configured
- Gitleaks secret scanning in `secret-scanning.yml`

The current statement coverage threshold is 95%. The measured local baseline on
2026-05-09 after the DTO-parity audit fixes was 95.02%.

Release automation validates Composer metadata, runs dependency audit, runs
`composer lint:all`, runs PHPUnit, creates GitHub releases, and triggers the
Packagist update only when the required secrets are configured.
