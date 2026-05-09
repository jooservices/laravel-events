<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventSourcing\Models;

use Carbon\Carbon;
use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $event_class
 * @property string|null $event_id
 * @property string|null $event_name
 * @property string|null $aggregate_id
 * @property string|null $aggregate_type
 * @property array $payload
 * @property array $metadata
 * @property int|string|null $schema_version
 * @property int|string|null $event_version
 * @property string|null $correlation_id
 * @property string|null $causation_id
 * @property int|string|null $user_id
 * @property Carbon|null $occurred_at
 */
class StoredEvent extends Model
{
    protected $connection;

    protected $table;

    protected $fillable = [
        'event_class',
        'event_id',
        'event_name',
        'aggregate_id',
        'aggregate_type',
        'payload',
        'metadata',
        'schema_version',
        'event_version',
        'correlation_id',
        'causation_id',
        'user_id',
        'occurred_at',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = config('events.connection', 'mongodb');
        $this->table = config('events.eventsourcing.collection', 'stored_events');
    }
}
