<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * @property string $entity_type
 * @property string $entity_id
 * @property string $action
 * @property array<string, mixed> $prev
 * @property array<string, mixed> $changed
 * @property array<string, mixed> $diff
 * @property array<string, mixed> $meta
 * @property int|string|null $user_id
 */
class EventLogEntry extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'event_logs';

    protected $fillable = ['entity_type', 'entity_id', 'action', 'prev', 'changed', 'diff', 'meta', 'user_id'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $connection = config('events.connection', 'mongodb');
        if (is_string($connection)) {
            $this->connection = $connection;
        }

        $table = config('events.event_log.collection', 'event_logs');
        if (is_string($table)) {
            $this->table = $table;
        }
    }
}
