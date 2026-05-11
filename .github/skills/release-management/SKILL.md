# Release Management

Use this skill for release preparation, branch sync, version tags, changelog updates, Packagist automation, and production readiness.

## What

`master` is production/release state and `develop` is development state. Normal features start from `develop` and target `develop`; approved releases are prepared from a short-lived `release/<version>` branch cut from `develop`, merged into `master`, tagged from `master`, and then synced back into `develop`. Hotfixes start from `master`, target `master`, and are synced back to `develop`.

## Why

This package is distributed through Composer. Release branches, tags, docs, and Composer metadata must stay trustworthy because consumers may install directly from Packagist or GitHub tags.

## How

- Inspect branch state and uncommitted changes before release work.
- Do not force push or delete branches until they are verified merged.
- Keep completed feature groups committed with author `Viet Vu <jooservices@gmail.com>`.
- Update `CHANGELOG.md`, README, and docs for public behavior changes.
- Run `composer validate --strict`, `composer install`, `composer lint:all`, `composer test`, `composer test:coverage`, `composer check`, and `composer ci` before release handoff.
- Confirm guarded release steps do not fail when optional Packagist or analysis secrets are missing.
- Stop and ask if branch state, tags, docs, CI, or dependency changes conflict.
- Keep Laravel 12 / PHP 8.5 support, Pint authority, real MongoDB tests, and docs synchronization visible in release readiness checks.
