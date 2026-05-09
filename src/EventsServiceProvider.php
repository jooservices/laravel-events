<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use JooServices\LaravelEvents\Console\InstallIndexesCommand;
use JooServices\LaravelEvents\EventLog\EventLogSubscriber;
use JooServices\LaravelEvents\EventSourcing\EventSourcingSubscriber;
use JooServices\LaravelEvents\Query\EventLogQueryService;
use JooServices\LaravelEvents\Query\StoredEventQueryService;

class EventsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/events.php', 'events');
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
