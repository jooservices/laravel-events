<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\EventLog\Models;

use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\Tests\TestCase;

class EventLogEntryTest extends TestCase
{
    public function test_constructor_sets_connection_and_collection_from_config(): void
    {
        config()->set('events.connection', 'mongodb');
        config()->set('events.event_log.collection', 'event_logs');
        $model = new EventLogEntry;
        $this->assertSame('mongodb', $model->getConnectionName());
        $this->assertSame('event_logs', $model->getTable());
    }

    public function test_fillable_attributes(): void
    {
        $model = new EventLogEntry;
        $this->assertSame(
            ['entity_type', 'entity_id', 'action', 'prev', 'changed', 'diff', 'meta', 'user_id'],
            $model->getFillable()
        );
    }
}
