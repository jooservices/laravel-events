<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use JOOservices\LaravelEvents\Console\InstallIndexesCommand;
use JOOservices\LaravelEvents\EventLog\EventLogSubscriber;
use JOOservices\LaravelEvents\EventSourcing\EventSourcingSubscriber;
use JOOservices\LaravelEvents\Query\EventLogQueryService;
use JOOservices\LaravelEvents\Query\StoredEventQueryService;
use JOOservices\LaravelEvents\Serialization\ArrayEventSerializer;
use JOOservices\LaravelEvents\Serialization\EventSerializerInterface;

class EventsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/events.php', 'events');
        $this->app->bind(EventSerializerInterface::class, ArrayEventSerializer::class);
        $this->app->singleton(EventService::class);
        $this->app->singleton(StoredEventQueryService::class);
        $this->app->singleton(EventLogQueryService::class);
    }

    public function boot(Dispatcher $events): void
    {
        $events->subscribe(EventSourcingSubscriber::class);
        $events->subscribe(EventLogSubscriber::class);

        if ($this->app->runningInConsole()) {
            $this->commands([InstallIndexesCommand::class]);
            $this->publishes([
                __DIR__.'/../config/events.php' => config_path('events.php'),
            ], 'laravel-events-config');
        }
    }
}
