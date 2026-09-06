<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\EventSourcing;

use JOOservices\LaravelEvents\EventSourcing\EventCategory;
use PHPUnit\Framework\TestCase;

class EventCategoryTest extends TestCase
{
    public function test_all_returns_documented_categories(): void
    {
        self::assertSame([
            EventCategory::DOMAIN->value,
            EventCategory::INTEGRATION->value,
            EventCategory::AUDIT->value,
            EventCategory::SYSTEM->value,
        ], EventCategory::all());
    }
}
