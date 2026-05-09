# Redaction example

Configure recursive redaction:

In `config/events.php`:

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

Then dispatch normally:

```php
event(new OrderCreated('ORD-1', ['password' => 'secret']));
```

The persisted payload stores `password` as `[REDACTED]`.
