# jooservices/laravel-events

This file adds project-only rules.

- PHP `^8.5`, Laravel package: `laravel/framework` `^12|^13`, MongoDB via `mongodb/laravel-mongodb` `^5.7`
- Runtime deps: `jooservices/dto` `^3.2`, `jooservices/exceptions` `^4.0`
- Namespace **must** be `JOOservices\LaravelEvents\` (uppercase `OO`)
- Persist Laravel-native events only (EventSourcing + EventLog). No projection, replay, or AI runtime
- Pint `per`; PHPStan + Larastan; PHPCS + PHPMD; CaptainHook required — never `--no-verify`
- Commands: `composer lint`, `composer test`, `composer check`, `composer ci`
