# DTO-Style Records

Use this skill when changing `src/Data/*`, serializer output, metadata records, or data-shape documentation.

## What

This package uses small typed data records to normalize stored event, event log, metadata, diff, and envelope data. It uses `jooservices/dto` as a repository-quality and documentation baseline, not as a runtime dependency or domain model to copy wholesale.

## Why

Typed records make persistence shapes easier to test and document while keeping package users free to own their application DTOs and event payloads.

## How

- Inspect current data record constructors, `toArray()` output, model fillable fields, serializer behavior, and tests before changing shape.
- Keep records simple and immutable where practical.
- Preserve backward-compatible array keys and MongoDB fields.
- Add nullable additive fields only when older stored documents remain readable.
- Do not import DTO package features such as schema generation, casting, validation, or OpenAPI output unless explicitly requested.
- Update API reference and examples when record output changes.
- Run Pint, PHPStan/Larastan, PHPCS, PHPMD, PHP-CS-Fixer, and tests before done.
- Use Laravel 12 / PHP 8.5 package standards for all record and serializer changes.
