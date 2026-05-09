# Security Hardening

Review secrets, tokens, cookies, authorization headers, PII, redaction, metadata,
retention, TTL indexes, and dependency changes.

Redaction is defensive masking only. Applications should avoid dispatching
secrets in the first place.

Required behavior:

- inspect the real code, config, docs, and tests before changing security behavior
- keep Laravel 12 / PHP 8.5 compatibility in mind
- never store secrets, raw tokens, private keys, or unnecessary PII in payloads or metadata
- test persistence and TTL/index behavior with real MongoDB when storage changes
- keep Pint as the formatting authority
- update docs when security guidance or public behavior changes
- stop and ask when redaction, authorization, retention, or compliance requirements are unclear
