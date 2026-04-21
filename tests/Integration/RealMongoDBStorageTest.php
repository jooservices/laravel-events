<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Integration;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Event;
use JooServices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JooServices\LaravelEvents\EventLog\Models\EventLogEntry;
use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use MongoDB\Laravel\Connection;

class RealMongoDBStorageTest extends MongoDBIntegrationTestCase
{
    private const EVIDENCE_FILE = __DIR__.'/../../build/mongodb_evidence.json';

    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->mongodbAvailable()) {
            $this->markTestSkipped('MongoDB is not available at '.env('MONGODB_URI', 'mongodb://127.0.0.1:27017'));
        }
    }

    private function mongodbAvailable(): bool
    {
        try {
            $connection = $this->app->make('db')->connection('mongodb');
            if (! $connection instanceof Connection) {
                return false;
            }
            $connection->getDatabase()->command(['ping' => 1]);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function test_event_sourcing_stored_to_real_mongodb_and_evidence_written(): void
    {
        $event = new class implements EventSourcingInterface
        {
            public function payload(): array
            {
                return ['order_id' => 'ORD-999', 'amount' => 99.99, 'at' => now()->toIso8601String()];
            }

            public function aggregateId(): ?string
            {
                return 'ORD-999';
            }
        };

        Event::dispatch($event);

        $stored = StoredEvent::on('mongodb')->orderBy('_id', 'desc')->first();
        $this->assertNotNull($stored, 'StoredEvent should exist in MongoDB');
        $this->assertSame($event::class, $stored->event_class);
        $this->assertSame('ORD-999', $stored->aggregate_id);
        $this->assertSame(
            ['order_id' => 'ORD-999', 'amount' => 99.99, 'at' => $event->payload()['at']],
            $stored->payload
        );
        $this->assertNull($stored->user_id, 'Guest: user_id should be null');

        $evidence = [
            'test' => 'event_sourcing',
            'collection' => config('events.eventsourcing.collection', 'stored_events'),
            'stored_at' => now()->toIso8601String(),
            'document' => $stored->toArray(),
        ];
        $this->writeEvidence('stored_events', $evidence);
    }

    public function test_event_log_stored_to_real_mongodb_and_evidence_written(): void
    {
        $loggableEvent = new class implements LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Order';
            }

            public function getLoggableId(): string
            {
                return '42';
            }

            public function getPrev(): array
            {
                return ['status' => 'pending', 'total' => 50.00];
            }

            public function getChanged(): array
            {
                return ['status' => 'completed', 'total' => 75.00];
            }
        };

        Event::dispatch($loggableEvent);

        $entry = EventLogEntry::on('mongodb')->orderBy('_id', 'desc')->first();
        $this->assertNotNull($entry, 'EventLogEntry should exist in MongoDB');
        $this->assertSame('Order', $entry->entity_type);
        $this->assertSame('42', $entry->entity_id);
        $this->assertSame('updated', $entry->action);
        $this->assertEquals(['status' => 'pending', 'total' => 50.00], $entry->prev);
        $this->assertEquals(['status' => 'completed', 'total' => 75.00], $entry->changed);
        $this->assertArrayHasKey('status', $entry->diff);
        $this->assertArrayHasKey('total', $entry->diff);
        $this->assertNull($entry->user_id, 'Guest: user_id should be null');

        $evidence = [
            'test' => 'event_log',
            'collection' => config('events.event_log.collection', 'event_logs'),
            'stored_at' => now()->toIso8601String(),
            'document' => $entry->toArray(),
        ];
        $this->writeEvidence('event_logs', $evidence);
    }

    public function test_event_sourcing_stores_user_id_when_logged_in(): void
    {
        $user = new User;
        $user->id = 100;
        $this->actingAs($user);

        $event = new class implements EventSourcingInterface
        {
            public function payload(): array
            {
                return ['action' => 'test_logged_in'];
            }

            public function aggregateId(): ?string
            {
                return 'agg-100';
            }
        };

        Event::dispatch($event);

        $stored = StoredEvent::on('mongodb')->orderBy('_id', 'desc')->first();
        $this->assertNotNull($stored);
        $this->assertSame(100, $stored->user_id);
    }

    public function test_event_log_stores_user_id_when_logged_in(): void
    {
        $user = new User;
        $user->id = 200;
        $this->actingAs($user);

        $loggableEvent = new class implements LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Test';
            }

            public function getLoggableId(): string
            {
                return '1';
            }

            public function getPrev(): array
            {
                return [];
            }

            public function getChanged(): array
            {
                return ['x' => 1];
            }
        };

        Event::dispatch($loggableEvent);

        $entry = EventLogEntry::on('mongodb')->orderBy('_id', 'desc')->first();
        $this->assertNotNull($entry);
        $this->assertSame(200, $entry->user_id);
    }

    private function writeEvidence(string $key, array $evidence): void
    {
        $dir = dirname(self::EVIDENCE_FILE);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $path = self::EVIDENCE_FILE;
        $existing = file_exists($path) ? (array) json_decode((string) file_get_contents($path), true) : [];
        $existing[$key] = $evidence;
        file_put_contents($path, json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->assertFileExists($path, 'Evidence file should be written');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        if (file_exists(self::EVIDENCE_FILE)) {
            fwrite(STDERR, "\n--- Evidence of MongoDB storage written to: ".realpath(self::EVIDENCE_FILE)." ---\n");
        }
    }
}
