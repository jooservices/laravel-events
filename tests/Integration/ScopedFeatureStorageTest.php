<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Integration;

use Illuminate\Testing\PendingCommand;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JOOservices\LaravelEvents\Query\EventLogQueryService;
use JOOservices\LaravelEvents\Query\StoredEventQueryService;
use MongoDB\Laravel\Connection;

class ScopedFeatureStorageTest extends MongoDBIntegrationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assertNotNull($this->app);
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
        $nestedPayload = $stored->payload['nested'] ?? null;
        $this->assertIsArray($nestedPayload);
        $this->assertSame('[REDACTED]', $nestedPayload['token'] ?? null);
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

        $result = $this->artisan('events:install-indexes');
        if ($result instanceof PendingCommand) {
            $result->assertSuccessful();
        } else {
            $this->assertSame(0, $result);
        }

        config()->set('events.retention.stored_events_days', 31);
        config()->set('events.retention.event_logs_days', 61);

        $rerun = $this->artisan('events:install-indexes');
        if ($rerun instanceof PendingCommand) {
            $rerun->assertSuccessful();
        } else {
            $this->assertSame(0, $rerun);
        }
    }

    public function test_query_services_cover_all_supported_filters(): void
    {
        $service = app(EventService::class);
        $from = now()->subMinute();

        $service->storeEvent(
            new \stdClass,
            ['order_id' => 'ORD-Q2'],
            'ORD-Q2',
            metadata: [
                'correlation_id' => 'corr-q2',
                'causation_id' => 'cmd-q2',
                'event_category' => 'domain',
            ],
        );
        $service->logChange(
            'orders',
            'ORD-Q2',
            'updated',
            ['status' => 'pending'],
            ['status' => 'paid'],
            ['status' => ['old' => 'pending', 'new' => 'paid']],
            ['correlation_id' => 'corr-q2', 'causation_id' => 'cmd-q2'],
        );

        $storedQueries = app(StoredEventQueryService::class);
        $eventQueries = app(EventLogQueryService::class);

        $this->assertSame('ORD-Q2', $storedQueries->byAggregateId('ORD-Q2')->first()?->aggregateId);
        $this->assertSame(\stdClass::class, $storedQueries->byEventName(\stdClass::class)->first()?->eventClass);
        $this->assertSame('domain', $storedQueries->byEventCategory('domain')->first()?->envelope?->eventCategory);
        $this->assertSame(
            'corr-q2',
            $storedQueries->byCorrelationId('corr-q2')->first()?->metadata['correlation_id'] ?? null
        );
        $this->assertSame(
            'cmd-q2',
            $storedQueries->byCausationId('cmd-q2')->first()?->metadata['causation_id'] ?? null
        );
        $this->assertGreaterThanOrEqual(1, $storedQueries->between($from, now())->count());
        $this->assertGreaterThanOrEqual(1, $storedQueries->latest(10)->count());

        $this->assertSame('ORD-Q2', $eventQueries->byEntity('orders', 'ORD-Q2')->first()?->entityId);
        $this->assertSame(
            'corr-q2',
            $eventQueries->byCorrelationId('corr-q2')->first()?->meta['correlation_id'] ?? null
        );
        $this->assertSame('cmd-q2', $eventQueries->byCausationId('cmd-q2')->first()?->meta['causation_id'] ?? null);
        $this->assertGreaterThanOrEqual(1, $eventQueries->between($from, now())->count());
        $this->assertGreaterThanOrEqual(1, $eventQueries->latest(10)->count());
    }

    public function test_install_indexes_command_can_drop_existing_indexes(): void
    {
        config()->set('events.retention.stored_events_days', 30);
        config()->set('events.retention.event_logs_days', 30);

        $createResult = $this->artisan('events:install-indexes');
        if ($createResult instanceof PendingCommand) {
            $createResult->assertSuccessful();
        } else {
            $this->assertSame(0, $createResult);
        }

        $dropResult = $this->artisan('events:install-indexes', ['--drop' => true, '--force' => true]);
        if ($dropResult instanceof PendingCommand) {
            $dropResult->assertSuccessful();
        } else {
            $this->assertSame(0, $dropResult);
        }

        $this->addToAssertionCount(1);
    }
}
