<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\EventSourcing\Models;

use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Tests\TestCase;

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
            [
                'event_class',
                'event_id',
                'event_name',
                'event_category',
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
            ],
            $model->getFillable()
        );
    }
}
