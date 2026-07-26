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

        $this->assertTrue(class_exists($class));
        $this->assertSame(
            EventService::class,
            (new ReflectionClass($class))->getName()
        );
        $this->assertStringStartsWith('JOOservices\\LaravelEvents\\', $class);
    }

    public function test_canonical_interface_and_trait_namespaces_are_available(): void
    {
        $this->assertTrue(interface_exists(EventSourcingInterface::class));
        $this->assertTrue(interface_exists(LoggableModelInterface::class));
        $this->assertTrue(trait_exists(HasEventSourcingDefaults::class));
        $this->assertTrue(trait_exists(DefaultsToUpdatedAction::class));
    }
}
