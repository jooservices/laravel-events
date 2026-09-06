<?php

declare(strict_types=1);

$positiveIntegerEnv = static function (string $key): ?int {
    $value = env($key);

    if ($value === null || $value === '') {
        return null;
    }

    $validated = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    return $validated === false ? null : (int) $validated;
};

return [
    'connection' => 'mongodb',

    /*
    |--------------------------------------------------------------------------
    | Context provider for request metadata
    |--------------------------------------------------------------------------
    | Invokable class name (or other config:cache-safe callable) that returns an
    | array merged into metadata (EventSourcing) and meta (EventLog). Closures
    | in published config break `php artisan config:cache` — use an invokable
    | class instead. Recommended keys: request_id, correlation_id, causation_id,
    | source, channel, reason_code, schema_version, tenant_id, user_id.
    | Return [] to disable. null disables the provider.
    */
    'context_provider' => null,

    'redaction' => [
        'enabled' => env('EVENTS_REDACTION_ENABLED', true),
        'keys' => [
            'password',
            'password_confirmation',
            'password_hash',
            'passwd',
            'token',
            'access_token',
            'refresh_token',
            'secret',
            'client_secret',
            'private_key',
            'secret_key',
            'api_key',
            'authorization',
            'cookie',
            'credit_card',
            'ssn',
        ],
        'replacement' => '[REDACTED]',
    ],

    'retention' => [
        'stored_events_days' => $positiveIntegerEnv('EVENTS_STORED_EVENTS_RETENTION_DAYS'),
        'event_logs_days' => $positiveIntegerEnv('EVENTS_EVENT_LOGS_RETENTION_DAYS'),
    ],

    'eventsourcing' => [
        'enabled' => env('EVENTS_EVENTSOURCING_ENABLED', true),
        /** MongoDB collection name for stored events */
        'collection' => env('EVENTS_STORED_EVENTS_COLLECTION', 'stored_events'),
        /**
         * Legacy TTL key. Prefer retention.stored_events_days.
         * Parsed like retention: positive int only; invalid values become null.
         */
        'ttl_days' => $positiveIntegerEnv('EVENTS_EVENTSOURCING_TTL_DAYS'),
    ],

    'event_log' => [
        'enabled' => env('EVENTS_EVENT_LOG_ENABLED', true),
        /** MongoDB collection name for event log entries */
        'collection' => env('EVENTS_EVENT_LOGS_COLLECTION', 'event_logs'),
        /**
         * Legacy TTL key. Prefer retention.event_logs_days.
         * Parsed like retention: positive int only; invalid values become null.
         */
        'ttl_days' => $positiveIntegerEnv('EVENTS_EVENT_LOG_TTL_DAYS'),
    ],
];
