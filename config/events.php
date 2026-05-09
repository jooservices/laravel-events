<?php

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
    | Callable (e.g. closure or invokable class) that returns an array merged
    | into metadata (EventSourcing) and meta (EventLog). Recommended keys:
    | request_id, correlation_id, causation_id, source, channel, reason_code,
    | schema_version, tenant_id. Return [] to disable.
    */
    'context_provider' => null,

    'redaction' => [
        'enabled' => env('EVENTS_REDACTION_ENABLED', true),
        'keys' => [
            'password',
            'password_confirmation',
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

    'retention' => [
        'stored_events_days' => $positiveIntegerEnv('EVENTS_STORED_EVENTS_RETENTION_DAYS'),
        'event_logs_days' => $positiveIntegerEnv('EVENTS_EVENT_LOGS_RETENTION_DAYS'),
    ],

    'eventsourcing' => [
        'enabled' => env('EVENTS_EVENTSOURCING_ENABLED', true),
        /** MongoDB collection name for stored events */
        'collection' => env('EVENTS_STORED_EVENTS_COLLECTION', 'stored_events'),
        /** Optional TTL: delete documents older than this many days (null = no TTL) */
        'ttl_days' => env('EVENTS_EVENTSOURCING_TTL_DAYS') ? (int) env('EVENTS_EVENTSOURCING_TTL_DAYS') : null,
    ],

    'event_log' => [
        'enabled' => env('EVENTS_EVENT_LOG_ENABLED', true),
        /** MongoDB collection name for event log entries */
        'collection' => env('EVENTS_EVENT_LOGS_COLLECTION', 'event_logs'),
        /** Optional TTL: delete documents older than this many days (null = no TTL) */
        'ttl_days' => env('EVENTS_EVENT_LOG_TTL_DAYS') ? (int) env('EVENTS_EVENT_LOG_TTL_DAYS') : null,
    ],
];
