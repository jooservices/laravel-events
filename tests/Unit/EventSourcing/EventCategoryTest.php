<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\EventSourcing;

use JOOservices\LaravelEvents\EventSourcing\EventCategory;
use PHPUnit\Framework\TestCase;

class EventCategoryTest extends TestCase
{
    public function test_all_returns_documented_categories(): void
    {
        $this->assertSame([
            EventCategory::DOMAIN,
            EventCategory::INTEGRATION,
            EventCategory::AUDIT,
            EventCategory::SYSTEM,
        ], EventCategory::all());
    }
}
