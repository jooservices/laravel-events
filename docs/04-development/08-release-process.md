# Release process

1. Keep `develop` and `master` synced according to [repository policy](../05-maintenance/01-risks-legacy-and-gaps.md).
2. Ensure local work is clean.
3. Run `composer ci` on the implementation branch before release handoff.
4. Merge the approved feature or fix branch into `develop`.
5. Create `release/<version>` from the latest `develop`.
6. Update `CHANGELOG.md`, `README.md`, and any release-facing docs on the release branch.
7. Open a pull request from `release/<version>` to `master`.
8. After the release PR is merged into `master`, create the version tag from `master`.
9. Let GitHub Actions create the GitHub release and package notifications.
10. Merge `master` back into `develop` after the release is complete.

Use `develop` as the integration branch. Branch hotfixes from `master` and sync
them back into `develop` after release.

Do not release with failing checks or undocumented breaking changes.
