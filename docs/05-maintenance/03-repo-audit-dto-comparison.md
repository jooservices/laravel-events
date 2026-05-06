# Repository audit against jooservices/dto

This audit compares `jooservices/laravel-events` with the current local
`jooservices/dto` baseline at commit `ee0c1de`. DTO is used as a maturity
baseline only. Domain-specific DTO documentation, runtime code, and schema
features are not copied into this package.

## Already consistent

- Package metadata, MIT license, Composer package type, support links, and
  author metadata are present.
- PHP 8.5 and Laravel 12 package compatibility are declared.
- Composer scripts cover `test`, `test:coverage`, `lint`, `lint:all`,
  `lint:fix`, `check`, and `ci`.
- CaptainHook install hooks are wired through Composer install and update
  scripts.
- Pint, PHPCS, PHPStan/Larastan, and PHPMD are present, with Pint treated as
  the primary formatter.
- CI exists for `master` and `develop`, including Composer audit, linting,
  MongoDB-backed tests, coverage generation, release automation, scorecard,
  PR labeling, semantic PR validation, and Dependabot.
- The package has Laravel auto-discovery through the service provider.
- The code already includes MongoDB-backed event sourcing and event log
  persistence, real MongoDB integration tests, metadata helper support, and
  index installation command coverage.
- Root `AGENTS.md`, `CLAUDE.md`, `.editorconfig`, `.gitattributes`,
  `.gitleaks.toml`, `README.md`, `CHANGELOG.md`, and `LICENSE` are present.

## Intentionally different

- This package depends on Laravel 12 and `mongodb/laravel-mongodb`; DTO is a
  framework-independent PHP library.
- This package uses Larastan and Orchestra Testbench; DTO uses plain PHPStan
  and PHPUnit integration fixtures.
- This package has MongoDB service requirements in CI and integration tests;
  DTO does not.
- Package docs must describe Laravel-native event sourcing and audit event log
  persistence only, not DTO hydration, casting, schema generation, or data
  object internals.
- Code examples, configuration, and package boundaries are specific to event
  persistence and audit logging.

## Missing

- Docs are still in a flat legacy structure instead of the current DTO-style
  structure:
  `docs/00-architecture`, `docs/01-getting-started`, `docs/02-user-guide`,
  `docs/03-examples`, `docs/04-development`, and `docs/05-maintenance`.
- `SECURITY.md`, `CONTRIBUTING.md`, `CODE_OF_CONDUCT.md`,
  `.github/pull_request_template.md`, and issue templates are missing.
- DTO-style AI/editor guidance is missing:
  `.github/skills`, `.github/instructions`, `.github/prompts`, `ai/skills`,
  `.cursor/rules`, `.claude/commands`, `antigravity/prompts`, and
  `jetbrains/prompts`.
- `phpdoc.dist.xml` and `sonar-project.properties` are missing.
- Composer tooling does not include PHP-CS-Fixer. DTO has it, but this package
  currently uses Pint plus PHPCS for style. Adding PHP-CS-Fixer should be
  evaluated carefully to avoid conflicting formatters.
- Optional model observer helper is not implemented.
- Root README and docs do not yet cover all current and planned package
  quality gates, branch workflow, redaction, retention, query APIs, or
  maintenance notes.

## Implemented in this pass

- Restructure documentation to the current DTO-style docs tree while
  preserving useful package-specific content.
- Add governance, issue, pull request, and AI/editor guidance adapted to
  `laravel-events`.
- Align Composer tooling and CI with DTO maturity where appropriate for a
  Laravel MongoDB package.
- Add scoped package features:
  DTO-backed event and audit records, lightweight query APIs, recursive
  redaction, metadata helper refinements, TTL index support, and bulk record
  APIs where they fit the current architecture cleanly.
- Update tests and documentation to cover the implemented behavior.

## Deferred

- Dashboard, reporting UI, analytics, projection framework, AI runtime, and
  unrelated Laravel application features are explicitly out of scope.
- Codecov, Sonar, Codacy, or Fortify integration will only be added when the
  repository has safe optional configuration or required secrets. CI must not
  contain fake success or guaranteed-failing secret-dependent steps.
- Optional model observer helpers are deferred because they risk hidden model
  observation, app-specific assumptions, and scope creep unless designed as a
  separate small opt-in feature.
- PHP-CS-Fixer will be deferred unless it can be configured as a non-conflicting
  secondary style check under Pint's primary formatting authority.
