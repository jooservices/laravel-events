<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Connection;

class InstallIndexesCommand extends Command
{
    protected $signature = 'events:install-indexes
                            {--drop : Drop indexes instead of creating them}
                            {--force : Skip confirmation when dropping}';

    protected $description = 'Create (or drop) recommended MongoDB indexes for Laravel event persistence collections.';

    public function handle(): int
    {
        $connectionName = config('events.connection', 'mongodb');
        $connection = DB::connection($connectionName);

        if (! $connection instanceof Connection) {
            $this->error('Events package requires a MongoDB connection. Configure config("events.connection").');

            return self::FAILURE;
        }

        if ($this->option('drop')) {
            if (! $this->option('force') && ! $this->confirm('Drop indexes? This does not delete data.')) {
                return self::SUCCESS;
            }
            $this->dropIndexes($connection);

            return self::SUCCESS;
        }

        $this->createStoredEventsIndexes($connection);
        $this->createEventLogsIndexes($connection);
        $this->info('Indexes created successfully.');

        return self::SUCCESS;
    }

    private function createStoredEventsIndexes(Connection $connection): void
    {
        $collectionName = config('events.eventsourcing.collection', 'stored_events');
        $collection = $connection->getCollection($collectionName);

        $collection->createIndex(['aggregate_id' => 1]);
        $collection->createIndex(['aggregate_id' => 1, 'created_at' => 1]);
        $collection->createIndex(['event_class' => 1]);
        $collection->createIndex(['event_class' => 1, 'created_at' => 1]);
        $collection->createIndex(['metadata.correlation_id' => 1]);
        $collection->createIndex(['metadata.causation_id' => 1]);
        $collection->createIndex(['user_id' => 1]);

        $ttlDays = config('events.retention.stored_events_days', config('events.eventsourcing.ttl_days'));
        if ($ttlDays !== null && $ttlDays > 0) {
            $collection->createIndex(
                ['created_at' => 1],
                ['expireAfterSeconds' => $ttlDays * 86400, 'name' => 'ttl_created_at']
            );
        } else {
            $collection->createIndex(['created_at' => 1]);
        }

        $this->line("  [stored_events] indexes created (collection: {$collectionName})");
    }

    private function createEventLogsIndexes(Connection $connection): void
    {
        $collectionName = config('events.event_log.collection', 'event_logs');
        $collection = $connection->getCollection($collectionName);

        $collection->createIndex(['entity_type' => 1, 'entity_id' => 1]);
        $collection->createIndex(['entity_type' => 1, 'entity_id' => 1, 'created_at' => -1]);
        $collection->createIndex(['action' => 1]);
        $collection->createIndex(['action' => 1, 'created_at' => -1]);
        $collection->createIndex(['meta.correlation_id' => 1]);
        $collection->createIndex(['meta.causation_id' => 1]);
        $collection->createIndex(['user_id' => 1]);

        $ttlDays = config('events.retention.event_logs_days', config('events.event_log.ttl_days'));
        if ($ttlDays !== null && $ttlDays > 0) {
            $collection->createIndex(
                ['created_at' => 1],
                ['expireAfterSeconds' => $ttlDays * 86400, 'name' => 'ttl_created_at']
            );
        } else {
            $collection->createIndex(['created_at' => 1]);
        }

        $this->line("  [event_logs] indexes created (collection: {$collectionName})");
    }

    private function dropIndexes(Connection $connection): void
    {
        $storedCollection = config('events.eventsourcing.collection', 'stored_events');
        $logCollection = config('events.event_log.collection', 'event_logs');

        foreach ([$storedCollection, $logCollection] as $name) {
            try {
                $collection = $connection->getCollection($name);
                $collection->dropIndexes();
                $this->line("  [{$name}] indexes dropped.");
            } catch (\Throwable $e) {
                $this->warn("  [{$name}] drop failed: ".$e->getMessage());
            }
        }
    }
}
