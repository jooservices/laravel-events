<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $entity_type
 * @property string $entity_id
 * @property string $action
 * @property array $prev
 * @property array $changed
 * @property array $diff
 * @property array $meta
 * @property int|string|null $user_id
 */
class EventLogEntry extends Model
{
    protected $connection;

    protected $table;

    protected $fillable = ['entity_type', 'entity_id', 'action', 'prev', 'changed', 'diff', 'meta', 'user_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->connection = config('events.connection', 'mongodb');
        $this->table = config('events.event_log.collection', 'event_logs');
    }
}
