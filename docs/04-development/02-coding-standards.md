# Coding standards

- Use PHP 8.5 language features intentionally.
- Follow Laravel 12 package conventions.
- Keep package scope focused on event sourcing and audit event log persistence.
- Prefer SOLID, KISS, DRY, and YAGNI.
- Keep public APIs developer-friendly and backward compatible where practical.
- Convert unstructured input to typed package data near the service boundary.
- Keep MongoDB persistence details inside model, repository, service, or command
  boundaries.

Pint is the primary formatter and wins when style tools disagree.
