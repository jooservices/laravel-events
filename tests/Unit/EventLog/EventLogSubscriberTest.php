<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\EventLog;

use Illuminate\Foundation\Auth\User;
use JOOservices\LaravelEvents\EventLog\Contracts\HasLogAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JOOservices\LaravelEvents\EventLog\EventLogSubscriber;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\Support\DiffHelper;
use JOOservices\LaravelEvents\Tests\TestCase;
use Mockery;

class EventLogSubscriberTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_log_model_change_calls_event_service_with_diff(): void
    {
        $event = new class implements LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Order';
            }

            public function getLoggableId(): string
            {
                return '42';
            }

            /** @return array<string, mixed> */
            public function getPrev(): array
            {
                return ['status' => 'pending'];
            }

            /** @return array<string, mixed> */
            public function getChanged(): array
            {
                return ['status' => 'completed'];
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('logChange')
            ->once()
            ->with(
                'Order',
                '42',
                'updated',
                ['status' => 'pending'],
                ['status' => 'completed'],
                ['status' => ['old' => 'pending', 'new' => 'completed']],
                Mockery::on(fn (array $meta) => array_key_exists('user_id', $meta))
            );

        $diffHelper = new DiffHelper;
        $subscriber = new EventLogSubscriber($eventService, $diffHelper);
        $subscriber->logModelChange($event);
        $this->addToAssertionCount(1);
    }

    public function test_subscribe_does_not_register_when_disabled(): void
    {
        config()->set('events.event_log.enabled', false);
        $this->assertNotNull($this->app);
        $subscriber = $this->app->make(EventLogSubscriber::class);
        $dispatcher = Mockery::mock('Illuminate\Contracts\Events\Dispatcher');
        $dispatcher->shouldReceive('listen')->never();
        $subscriber->subscribe($dispatcher);
        $this->addToAssertionCount(1);
    }

    public function test_log_model_change_passes_null_user_id_when_guest(): void
    {
        $this->assertNull(auth()->id());
        $event = new class implements LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Item';
            }

            public function getLoggableId(): string
            {
                return '99';
            }

            /** @return array<string, mixed> */
            public function getPrev(): array
            {
                return [];
            }

            /** @return array<string, mixed> */
            public function getChanged(): array
            {
                return ['name' => 'x'];
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('logChange')
            ->once()
            ->with(
                'Item',
                '99',
                'updated',
                [],
                ['name' => 'x'],
                ['name' => ['old' => null, 'new' => 'x']],
                ['user_id' => null]
            );

        $subscriber = new EventLogSubscriber($eventService, new DiffHelper);
        $subscriber->logModelChange($event);
        $this->addToAssertionCount(1);
    }

    public function test_log_model_change_passes_logged_in_user_id(): void
    {
        $user = new User;
        $user->id = 33;
        $this->actingAs($user);

        $event = new class implements LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Item';
            }

            public function getLoggableId(): string
            {
                return '99';
            }

            /** @return array<string, mixed> */
            public function getPrev(): array
            {
                return [];
            }

            /** @return array<string, mixed> */
            public function getChanged(): array
            {
                return ['name' => 'x'];
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('logChange')
            ->once()
            ->with(
                'Item',
                '99',
                'updated',
                [],
                ['name' => 'x'],
                ['name' => ['old' => null, 'new' => 'x']],
                ['user_id' => 33]
            );

        $subscriber = new EventLogSubscriber($eventService, new DiffHelper);
        $subscriber->logModelChange($event);
        $this->addToAssertionCount(1);
    }

    public function test_log_model_change_uses_action_from_has_log_action(): void
    {
        $event = new class implements HasLogAction, LoggableModelInterface
        {
            public function getLoggableType(): string
            {
                return 'Order';
            }

            public function getLoggableId(): string
            {
                return '42';
            }

            /** @return array<string, mixed> */
            public function getPrev(): array
            {
                return [];
            }

            /** @return array<string, mixed> */
            public function getChanged(): array
            {
                return ['status' => 'pending'];
            }

            public function getAction(): string
            {
                return 'created';
            }
        };

        $eventService = Mockery::mock(EventService::class);
        $eventService->shouldReceive('logChange')
            ->once()
            ->with(
                'Order',
                '42',
                'created',
                [],
                ['status' => 'pending'],
                ['status' => ['old' => null, 'new' => 'pending']],
                Mockery::on(fn (array $meta) => array_key_exists('user_id', $meta))
            );

        $subscriber = new EventLogSubscriber($eventService, new DiffHelper);
        $subscriber->logModelChange($event);
        $this->addToAssertionCount(1);
    }
}
