<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit;

use JooServices\LaravelEvents\EventService;
use JooServices\LaravelEvents\EventsServiceProvider;
use JooServices\LaravelEvents\Serialization\ArrayEventSerializer;
use JooServices\LaravelEvents\Serialization\EventSerializerInterface;
use JooServices\LaravelEvents\Tests\TestCase;

class EventsServiceProviderTest extends TestCase
{
    public function test_config_is_merged(): void
    {
        $this->assertSame('mongodb', config('events.connection'));
        $this->assertTrue(config('events.eventsourcing.enabled'));
        $this->assertSame('stored_events', config('events.eventsourcing.collection'));
        $this->assertSame('event_logs', config('events.event_log.collection'));
    }

    public function test_event_service_is_singleton(): void
    {
        $this->assertNotNull($this->app);
        $a = $this->app->make(EventService::class);
        $b = $this->app->make(EventService::class);
        $this->assertSame($a, $b);
    }

    public function test_provider_registers_subscribers(): void
    {
        $this->assertNotNull($this->app);
        $provider = $this->app->getProvider(EventsServiceProvider::class);
        $this->assertInstanceOf(EventsServiceProvider::class, $provider);
    }

    public function test_provider_binds_default_event_serializer(): void
    {
        $this->assertNotNull($this->app);
        $this->assertInstanceOf(ArrayEventSerializer::class, $this->app->make(EventSerializerInterface::class));
    }
}
