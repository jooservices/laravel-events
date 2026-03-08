<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Integration;

use JooServices\LaravelEvents\Tests\TestCase;
use MongoDB\Laravel\MongoDBServiceProvider;

/**
 * Base for integration tests that use a real MongoDB connection.
 * Registers MongoDB driver and configures database.connections.mongodb.
 */
abstract class MongoDBIntegrationTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return array_merge(parent::getPackageProviders($app), [
            MongoDBServiceProvider::class,
        ]);
    }

    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('database.connections.mongodb', [
            'driver' => 'mongodb',
            'dsn' => env('MONGODB_URI', 'mongodb://127.0.0.1:27017'),
            'database' => env('MONGODB_DATABASE', 'laravel_events_test'),
        ]);
    }
}
