# Tech stack

- PHP 8.5
- Laravel 12
- `mongodb/laravel-mongodb` for MongoDB Eloquent models
- Orchestra Testbench for package integration tests
- PHPUnit for tests and coverage
- Pint, PHPCS, PHPStan/Larastan, and PHPMD for quality gates
- CaptainHook for local Git hooks
- GitHub Actions for CI, release, scorecard, PR labels, and semantic PR title checks

MongoDB is a hard runtime requirement for persisted event and audit data. Tests
that exercise persistence should use a real MongoDB connection whenever
possible.
