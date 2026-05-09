<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Integration;

use JooServices\LaravelEvents\Data\EventLogData;
use JooServices\LaravelEvents\Data\StoredEventData;
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventService;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Query\EventLogQueryService;
use JooServices\LaravelEvents\Query\StoredEventQueryService;
use MongoDB\Laravel\Connection;

class ScopedFeatureStorageTest extends MongoDBIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $connection = $this->app->make('db')->connection('mongodb');
        if (! $connection instanceof Connection) {
            $this->markTestSkipped('MongoDB is not available.');
        }

        try {
            $connection->getDatabase()->command(['ping' => 1]);
        } catch (\Throwable) {
            $this->markTestSkipped('MongoDB is not available.');
        }

        StoredEvent::on('mongodb')->delete();
        EventLogEntry::on('mongodb')->delete();
    }

    public function test_query_services_filter_persisted_records(): void
    {
        $service = app(EventService::class);
        $service->storeEvent(
            new \stdClass,
            ['order_id' => 'ORD-Q'],
            'ORD-Q',
            metadata: ['correlation_id' => 'corr-q', 'causation_id' => 'cmd-q'],
        );
        $service->logChange(
            'orders',
            'ORD-Q',
            'updated',
            ['status' => 'pending'],
            ['status' => 'paid'],
            ['status' => ['old' => 'pending', 'new' => 'paid']],
            ['correlation_id' => 'corr-q', 'causation_id' => 'cmd-q'],
        );

        $stored = app(StoredEventQueryService::class)->byCorrelationId('corr-q');
        $logs = app(EventLogQueryService::class)->byEntity('orders', 'ORD-Q');

        $this->assertSame('ORD-Q', $stored->first()?->aggregateId);
        $this->assertSame('orders', $logs->first()?->entityType);
    }

    public function test_redaction_applies_to_persisted_event_and_log_records(): void
    {
        $service = app(EventService::class);
        $service->storeEvent(new \stdClass, ['password' => 'secret', 'nested' => ['token' => 'abc']], 'redact-1');
        $service->logChange(
            'users',
            '1',
            'updated',
            ['password' => 'old'],
            ['password' => 'new'],
            ['password' => ['old' => 'old', 'new' => 'new']],
            ['authorization' => 'Bearer abc'],
        );

        $stored = StoredEvent::on('mongodb')->where('aggregate_id', 'redact-1')->first();
        $entry = EventLogEntry::on('mongodb')->where('entity_type', 'users')->where('entity_id', '1')->first();

        $this->assertNotNull($stored);
        $this->assertNotNull($entry);
        $this->assertSame('[REDACTED]', $stored->payload['password']);
        $this->assertSame('[REDACTED]', $stored->payload['nested']['token']);
        $this->assertSame('[REDACTED]', $entry->prev['password']);
        $this->assertSame('[REDACTED]', $entry->meta['authorization']);
    }

    public function test_bulk_record_support_persists_many_records(): void
    {
        $service = app(EventService::class);
        $service->recordManyStoredEvents([
            new StoredEventData('BulkOne', ['id' => 1], 'bulk-1'),
            new StoredEventData('BulkTwo', ['id' => 2], 'bulk-2'),
        ]);
        $service->recordManyEventLogs([
            new EventLogData('bulk', '1', 'created', changed: ['id' => 1]),
            new EventLogData('bulk', '2', 'created', changed: ['id' => 2]),
        ]);

        $this->assertSame(2, StoredEvent::on('mongodb')->whereIn('aggregate_id', ['bulk-1', 'bulk-2'])->count());
        $this->assertSame(2, EventLogEntry::on('mongodb')->where('entity_type', 'bulk')->count());
    }

    public function test_bulk_record_support_merges_context_and_user_attribution(): void
    {
        config()->set('events.context_provider', fn (): array => [
            'correlation_id' => 'bulk-context',
            'user_id' => 'context-user',
        ]);

        $service = app(EventService::class);
        $service->recordManyStoredEvents([
            new StoredEventData('BulkContextEvent', ['id' => 1], 'bulk-context-1'),
        ]);
        $service->recordManyEventLogs([
            new EventLogData('bulk-context', '1', 'updated', changed: ['id' => 1]),
        ]);

        $stored = StoredEvent::on('mongodb')->where('aggregate_id', 'bulk-context-1')->first();
        $log = EventLogEntry::on('mongodb')->where('entity_type', 'bulk-context')->first();

        $this->assertSame('bulk-context', $stored?->metadata['correlation_id'] ?? null);
        $this->assertSame('context-user', $stored?->metadata['user_id'] ?? null);
        $this->assertNull($stored?->user_id);
        $this->assertSame('bulk-context', $log?->meta['correlation_id'] ?? null);
        $this->assertSame('context-user', $log?->user_id);
    }

    public function test_install_indexes_command_creates_ttl_indexes_when_configured(): void
    {
        config()->set('events.retention.stored_events_days', 30);
        config()->set('events.retention.event_logs_days', 60);

        $this->artisan('events:install-indexes')->assertSuccessful();

        $connection = $this->app->make('db')->connection('mongodb');
        $this->assertInstanceOf(Connection::class, $connection);
        $storedIndexes = iterator_to_array(
            $connection->getCollection(config('events.eventsourcing.collection', 'stored_events'))->listIndexes()
        );
        $logIndexes = iterator_to_array(
            $connection->getCollection(config('events.event_log.collection', 'event_logs'))->listIndexes()
        );

        $this->assertTrue($this->hasTtlIndex($storedIndexes, 30 * 86400));
        $this->assertTrue($this->hasTtlIndex($logIndexes, 60 * 86400));
    }

    private function hasTtlIndex(array $indexes, int $seconds): bool
    {
        foreach ($indexes as $index) {
            if ((int) ($index['expireAfterSeconds'] ?? -1) === $seconds) {
                return true;
            }
        }

        return false;
    }
}
