# Payload redaction

Redaction is planned for this pass. Until it is implemented, applications must
avoid sending secrets, tokens, passwords, private keys, cookies, authorization
headers, or unnecessary PII to event payloads, metadata, audit snapshots, and
diffs.

The intended package behavior is defensive recursive masking before
persistence. It is not a replacement for application-level data minimization or
authorization.
