<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventSourcing\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $event_class
 * @property string|null $aggregate_id
 * @property array $payload
 * @property array $metadata
 * @property int|string|null $user_id
 * @property \DateTimeInterface|null $occurred_at
 */
class StoredEvent extends Model
{
    protected $connection;

    protected $table;

    protected $fillable = ['event_class', 'aggregate_id', 'payload', 'metadata', 'user_id', 'occurred_at'];

    protected $casts = [
        'payload' => 'array',
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = config('events.connection', 'mongodb');
        $this->table = config('events.eventsourcing.collection', 'stored_events');
    }
}
