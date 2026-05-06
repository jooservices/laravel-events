# Release process

1. Keep `develop` and `master` synced according to repository policy.
2. Ensure local work is clean.
3. Run `composer ci`.
4. Update `CHANGELOG.md`.
5. Tag releases from the release branch or `master` according to repo policy.
6. Let GitHub Actions create the GitHub release and package notifications.

Do not release with failing checks or undocumented breaking changes.
