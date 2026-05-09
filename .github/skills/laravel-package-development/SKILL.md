# Laravel Package Development

Use this skill for Laravel package code, service provider behavior, commands, config publishing, subscribers, and Testbench coverage in `jooservices/laravel-events`.

## What

This repository is a Laravel 12 package for MongoDB persistence of Event Sourcing and Event Log records. It should keep Laravel service provider discovery in `composer.json`, publish configuration through Laravel conventions, and test package integration through Orchestra Testbench.

## Why

Consumers expect package behavior to follow Laravel conventions. Hidden app assumptions, hardcoded model classes, tenant rules, auth policies, or event catalogs would make the package brittle and application-specific.

## How

- Inspect `composer.json`, `EventsServiceProvider`, `config/events.php`, subscribers, commands, and tests before editing.
- Preserve Laravel-native event dispatching; do not replace it with a custom bus.
- Keep public config keys backward compatible unless a breaking change is explicitly approved.
- Add tests for command registration, config merge/publish behavior, subscriber wiring, and service behavior when public behavior changes.
- Use PHP 8.5 typing and latest Laravel 12 package standards.
- Let Pint own formatting; keep PHP-CS-Fixer limited to non-conflicting PHPDoc cleanup.
- Run `composer validate --strict`, `composer lint:all`, `composer test`, and `composer check` before completion.
