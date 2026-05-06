# CI/CD

CI runs on `master`, `develop`, and pull requests targeting those branches.

Current jobs include:

- Composer audit
- Composer metadata validation
- Pint, PHPCS, PHPStan/Larastan, PHPMD, and narrow PHP-CS-Fixer PHPDoc checks
- PHPUnit coverage with a MongoDB service
- coverage artifact upload
- dependency review for pull requests
- optional Codecov upload when `CODECOV_TOKEN` is configured
- optional SonarQube Cloud analysis when `SONAR_TOKEN` is configured

Release automation validates version tags, creates GitHub releases, and can
notify Packagist when configured.
