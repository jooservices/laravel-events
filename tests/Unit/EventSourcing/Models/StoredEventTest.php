<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\EventSourcing\Models;

use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Tests\TestCase;

class StoredEventTest extends TestCase
{
    public function test_constructor_sets_connection_and_collection_from_config(): void
    {
        config()->set('events.connection', 'mongodb');
        config()->set('events.eventsourcing.collection', 'stored_events');
        $model = new StoredEvent;
        $this->assertSame('mongodb', $model->getConnectionName());
        $this->assertSame('stored_events', $model->getTable());
    }

    public function test_fillable_attributes(): void
    {
        $model = new StoredEvent;
        $this->assertSame(
            ['event_class', 'aggregate_id', 'payload', 'metadata', 'user_id', 'occurred_at'],
            $model->getFillable()
        );
    }
}
