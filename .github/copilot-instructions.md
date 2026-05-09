# Copilot Instructions For `jooservices/laravel-events`

Read [AGENTS.md](../AGENTS.md) as the primary repository policy.

When generating or editing code:

- preserve Laravel-native events and package discovery
- keep MongoDB persistence behavior configurable
- separate Event Sourcing records from Event Log audit records
- avoid app-specific event catalogs, tenant rules, authorization rules, projections, analytics, and AI runtime code
- update tests and docs when public behavior or commands change
- use `composer lint`, `composer lint:all`, `composer test`, and `composer check` as the main quality commands
- inspect the current source, branch, and docs before proposing changes
- use `develop` for normal work and target pull requests to `develop`; reserve `master` for release and hotfix work
- stop and ask when requirements, source truth, docs, CI, or package scope conflict
- consider work complete only after relevant validation passes or an exact environment limitation is reported
