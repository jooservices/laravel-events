<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\EventSourcing;

use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Auth\User;
use JooServices\LaravelEvents\EventService;
use JooServices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;
use JooServices\LaravelEvents\EventSourcing\EventSourcingSubscriber;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;
use JooServices\LaravelEvents\Tests\TestCase;
use Mockery;

class EventSourcingSubscriberTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_subscribe_registers_listener_and_persist_event_calls_service(): void
    {
        config()->set('events.eventsourcing.enabled', true);

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('storeEvent')
            ->once()
            ->with(Mockery::type(EventSourcingInterface::class), ['key' => 'value'], 'agg-1', null, null, [])
            ->andReturn(new StoredEvent);

        $subscriber = new EventSourcingSubscriber($eventService);
        $dispatcher = new Dispatcher;
        $subscriber->subscribe($dispatcher);

        $event = new class implements EventSourcingInterface
        {
            /** @return array<string, mixed> */
            public function payload(): array
            {
                return ['key' => 'value'];
            }

            public function aggregateId(): ?string
            {
                return 'agg-1';
            }
        };

        $dispatcher->dispatch($event);
        $this->addToAssertionCount(1);
    }

    public function test_persist_event_calls_event_service(): void
    {
        $event = new class implements EventSourcingInterface
        {
            /** @return array<string, mixed> */
            public function payload(): array
            {
                return ['data' => true];
            }

            public function aggregateId(): ?string
            {
                return null;
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('storeEvent')
            ->once()
            ->with($event, ['data' => true], null, null, null, []);

        $subscriber = new EventSourcingSubscriber($eventService);
        $subscriber->persistEvent($event);
        $this->addToAssertionCount(1);
    }

    public function test_persist_event_passes_logged_in_user_id(): void
    {
        $user = new User;
        $user->id = 5;
        $this->actingAs($user);

        $event = new class implements EventSourcingInterface
        {
            /** @return array<string, mixed> */
            public function payload(): array
            {
                return ['x' => 1];
            }

            public function aggregateId(): ?string
            {
                return 'a1';
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('storeEvent')
            ->once()
            ->with($event, ['x' => 1], 'a1', 5, null, []);

        $subscriber = new EventSourcingSubscriber($eventService);
        $subscriber->persistEvent($event);
        $this->addToAssertionCount(1);
    }
}
