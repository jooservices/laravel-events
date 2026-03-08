<?php

return [
    'connection' => env('EVENTS_MONGO_CONNECTION', 'mongodb'),

    /*
    |--------------------------------------------------------------------------
    | Context provider for request metadata
    |--------------------------------------------------------------------------
    | Callable (e.g. closure or invokable class) that returns an array merged
    | into metadata (EventSourcing) and meta (EventLog). Useful for request_id,
    | ip, user_agent, channel (web, api, queue, cron). Return [] to disable.
    */
    'context_provider' => null,

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
