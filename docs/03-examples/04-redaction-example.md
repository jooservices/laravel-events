# Redaction example

Redaction is planned for this pass. The intended configuration shape is:

```php
'redaction' => [
    'enabled' => true,
    'keys' => [
        'password',
        'token',
        'access_token',
        'refresh_token',
        'secret',
        'api_key',
        'authorization',
        'cookie',
    ],
    'replacement' => '[REDACTED]',
],
```

Do not send secrets to event payloads while redaction is not yet implemented.
