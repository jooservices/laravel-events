# CI/CD

CI runs on `master`, `develop`, and pull requests targeting those branches.

Current jobs include:

- Composer audit
- Pint, PHPCS, PHPStan/Larastan, and PHPMD
- PHPUnit coverage with a MongoDB service
- coverage artifact upload
- dependency review for pull requests

Release automation validates version tags, creates GitHub releases, and can
notify Packagist when configured.
