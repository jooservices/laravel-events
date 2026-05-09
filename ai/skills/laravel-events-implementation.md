# Laravel Events Implementation Skill

Use this for implementation tasks in `jooservices/laravel-events`.

Checklist:

- inspect the live source, branch state, docs, tests, and DTO baseline first
- start normal work from `develop`; target normal PRs to `develop`
- preserve `EventSourcingInterface`, `LoggableModelInterface`, config keys, and existing MongoDB field names
- keep changes small, additive, and Laravel-native
- use DTO-style data objects only where they clarify service boundaries
- keep MongoDB persistence configurable
- update tests and docs with public behavior changes
- stop and ask if a requirement implies a breaking change, schema rewrite, custom event bus, projections, replay side effects, outbox runtime, or AI runtime

Validation before completion:

```bash
composer validate --strict
composer lint:all
composer test
composer check
```
