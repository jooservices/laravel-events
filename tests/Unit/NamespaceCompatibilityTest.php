<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit;

use JOOservices\LaravelEvents\EventService;
use JOOservices\LaravelEvents\Tests\TestCase;
use ReflectionClass;

class NamespaceCompatibilityTest extends TestCase
{
    private const LEGACY_NAMESPACE = 'JooServices\\LaravelEvents\\';

    private const LEGACY_EVENT_SOURCING_INTERFACE = self::LEGACY_NAMESPACE
        .'EventSourcing\\Contracts\\EventSourcingInterface';

    private const LEGACY_LOGGABLE_MODEL_INTERFACE = self::LEGACY_NAMESPACE
        .'EventLog\\Contracts\\LoggableModelInterface';

    private const LEGACY_EVENT_SOURCING_DEFAULTS = self::LEGACY_NAMESPACE
        .'EventSourcing\\Concerns\\HasEventSourcingDefaults';

    private const LEGACY_UPDATED_ACTION_DEFAULTS = self::LEGACY_NAMESPACE
        .'EventLog\\Concerns\\DefaultsToUpdatedAction';

    public function test_legacy_class_namespace_resolves_to_canonical_class(): void
    {
        $legacyClass = 'JooServices\\LaravelEvents\\EventService';

        $this->assertTrue(class_exists($legacyClass));
        $this->assertSame(
            EventService::class,
            (new ReflectionClass($legacyClass))->getName()
        );
    }

    public function test_legacy_interface_and_trait_namespaces_are_available(): void
    {
        $this->assertTrue(interface_exists(self::LEGACY_EVENT_SOURCING_INTERFACE));
        $this->assertTrue(interface_exists(self::LEGACY_LOGGABLE_MODEL_INTERFACE));
        $this->assertTrue(trait_exists(self::LEGACY_EVENT_SOURCING_DEFAULTS));
        $this->assertTrue(trait_exists(self::LEGACY_UPDATED_ACTION_DEFAULTS));
    }
}
