# Laravel Events Audit Skill

Use this for audits, reviews, and DTO baseline comparisons.

Checklist:

- inspect actual source and GitHub state before judging
- compare conceptually with `jooservices/dto`, not by blind copy
- separate repo-standard gaps from runtime/package-scope gaps
- identify evidence by file and behavior
- report unclear or unverifiable items explicitly
- prioritize backward compatibility, Laravel-native events, MongoDB persistence, and package scope
- include validation command results when available

Audit areas:

- Composer scripts and lock validity
- CI/release/security workflows
- lint/static analysis/test configuration
- docs tree and link integrity
- AI guidance and branch workflow
- public API compatibility
- event metadata, redaction, retention, query, and storage behavior
