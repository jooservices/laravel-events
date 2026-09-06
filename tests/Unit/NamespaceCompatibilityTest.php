<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit;

use JOOservices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JOOservices\LaravelEvents\EventLog\Contracts\LoggableModelInterface;
use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\EventSourcing\Concerns\HasEventSourcingDefaults;
use JOOservices\LaravelEvents\EventSourcing\Contracts\EventSourcingInterface;
use JOOservices\LaravelEvents\Tests\TestCase;
use ReflectionClass;

class NamespaceCompatibilityTest extends TestCase
{
    public function test_canonical_class_namespace_resolves(): void
    {
        $class = EventService::class;

        self::assertTrue(class_exists($class));
        self::assertSame(
            EventService::class,
            (new ReflectionClass($class))->getName(),
        );
        self::assertStringStartsWith('JOOservices\\LaravelEvents\\', $class);
    }

    public function test_canonical_interface_and_trait_namespaces_are_available(): void
    {
        self::assertTrue(interface_exists(EventSourcingInterface::class));
        self::assertTrue(interface_exists(LoggableModelInterface::class));
        self::assertTrue(trait_exists(HasEventSourcingDefaults::class));
        self::assertTrue(trait_exists(DefaultsToUpdatedAction::class));
    }
}
