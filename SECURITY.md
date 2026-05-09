# Security Policy

## Supported versions

The latest stable release of `jooservices/laravel-events` is supported for
security fixes.

Older releases may be unsupported unless maintainers explicitly state otherwise
in release notes or repository documentation.

## Reporting a vulnerability

Do not open public GitHub issues for suspected vulnerabilities.

Report security concerns privately to [admin@jooservices.com](mailto:admin@jooservices.com) with:

- a clear summary of the issue
- affected package version
- impact and expected risk
- reproduction details or proof of concept when available

If you are unsure whether a report is security-related, contact maintainers
privately first.

## Scope

This policy covers repository-managed behavior such as:

- event sourcing persistence
- audit event log persistence
- metadata, payload, and redaction behavior
- MongoDB index and retention behavior
- dependency, CI, and security workflow configuration that affects package
  consumers or repository integrity

This package cannot secure application-specific authorization, tenant policy, or
which payloads an application chooses to dispatch.

## Non-security issues

Normal bugs, feature requests, questions, and documentation improvements should
use the standard GitHub issue templates instead of private security reporting.
