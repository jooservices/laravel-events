# Docs changelog

## 2026-05-06

- Adopted the current DTO-style documentation tree:
  `00-architecture`, `01-getting-started`, `02-user-guide`, `03-examples`,
  `04-development`, and `05-maintenance`.
- Moved legacy flat docs into the new reading order.
- Added maintenance docs under `docs/05-maintenance`.
- Kept AI guidance out of a removed `05-ai-contributor-guide` directory.

## 2026-05-09

- Documented additive stored event envelope fields and serializer extension
  points.
- Expanded AI skill entry points and maintenance roadmap notes for event store,
  outbox, replay, versioning, and observability.
- Clarified Event Log diff behavior for changed-only values and explicit nulls.
