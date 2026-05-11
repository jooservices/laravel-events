<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests;

use JOOservices\LaravelEvents\EventsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            EventsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        config()->set('events.connection', 'mongodb');
        config()->set('events.eventsourcing.enabled', true);
        config()->set('events.eventsourcing.collection', 'stored_events');
        config()->set('events.event_log.enabled', true);
        config()->set('events.event_log.collection', 'event_logs');
    }
}
