# Copilot Instructions For `jooservices/laravel-events`

Read [AGENTS.md](../AGENTS.md) as the primary repository policy.

When generating or editing code:

- preserve Laravel-native events and package discovery
- keep MongoDB persistence behavior configurable
- separate Event Sourcing records from Event Log audit records
- avoid app-specific event catalogs, tenant rules, authorization rules, projections, analytics, and AI runtime code
- update tests and docs when public behavior or commands change
- use `composer lint`, `composer lint:all`, `composer test`, and `composer check` as the main quality commands
