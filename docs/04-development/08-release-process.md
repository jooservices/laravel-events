# Release process

1. Keep `develop` and `master` synced according to [repository policy](../05-maintenance/01-risks-legacy-and-gaps.md).
2. Ensure local work is clean.
3. Run `composer ci`.
4. Update `CHANGELOG.md`.
5. Tag releases from `master` (production/release state); use `release/*` branches only if they are short-lived and merged into `master` before tagging.
6. Let GitHub Actions create the GitHub release and package notifications.

Use `develop` as the integration branch. Branch hotfixes from `master` and sync
them back into `develop` after release.

Do not release with failing checks or undocumented breaking changes.
