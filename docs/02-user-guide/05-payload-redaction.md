# Payload redaction

Redaction recursively masks configured keys before persistence.

```php
'redaction' => [
    'enabled' => true,
    'keys' => ['password', 'token', 'authorization'],
    'replacement' => '[REDACTED]',
],
```

Matching is case-insensitive and preserves the original array shape. Redaction
applies to stored event payload and metadata, plus event log `prev`, `changed`,
`diff`, and `meta`.

This is defensive masking only. Applications should still avoid dispatching
secrets, tokens, passwords, private keys, cookies, authorization headers, or
unnecessary PII.
