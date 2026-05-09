# Documentation Sync

Use this skill when changing public behavior, commands, configuration, CI, or
repository workflow in `jooservices/laravel-events`.

Required behavior:

- inspect the current source and docs before editing
- keep README, `docs/README.md`, user guides, examples, and maintenance notes aligned
- do not document outbox, replay, event-store adapters, AI runtime, authorization, redaction policies, or dashboards as implemented unless source and tests prove it
- keep the DTO-style docs tree intact:
  - `00-architecture`
  - `01-getting-started`
  - `02-user-guide`
  - `03-examples`
  - `04-development`
  - `05-maintenance`
- update links after moving or renaming docs
- run `composer lint:all` and `composer test` before considering docs-linked code work complete
