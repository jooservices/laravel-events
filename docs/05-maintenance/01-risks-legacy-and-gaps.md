# Risks, legacy, and gaps

- Documentation was recently moved from a flat structure into the current
  DTO-style docs tree.
- Query services and typed record DTOs are not yet implemented.
- Recursive redaction is not yet implemented.
- Bulk record APIs are not yet implemented.
- Optional model observer helpers should remain deferred unless they can be
  small, explicit, and opt-in.
- `composer.lock` policy needs to be aligned with the repository quality
  baseline during tooling work.
- Codecov, Sonar, Codacy, and Fortify integrations should only be enabled when
  repository configuration and secrets make them safe.
