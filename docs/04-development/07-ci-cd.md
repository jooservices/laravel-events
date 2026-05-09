# CI/CD

CI runs on `master`, `develop`, and pull requests targeting those branches.

Current jobs include:

- Composer audit
- Composer metadata validation
- Pint, PHPCS, PHPStan/Larastan, PHPMD, and narrow PHP-CS-Fixer PHPDoc checks
- PHPUnit coverage with a MongoDB service
- coverage artifact upload (minimum threshold: 80%)
- dependency review for pull requests
- optional Codecov upload when `CODECOV_TOKEN` is configured
- optional SonarQube Cloud analysis when `SONAR_TOKEN` is configured

The current temporary statement coverage threshold is 80%. The local baseline
measured on 2026-05-09 was 84.51%. DTO uses a 95% long-term target; this package
should raise the threshold toward 95% after query-service and command coverage
is expanded.

Release automation validates Composer metadata, runs dependency audit, runs
`composer lint:all`, runs PHPUnit, creates GitHub releases, and can notify
Packagist when configured.
