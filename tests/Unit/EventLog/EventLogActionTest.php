<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\EventLog;

use JOOservices\LaravelEvents\EventLog\Concerns\DefaultsToUpdatedAction;
use JOOservices\LaravelEvents\EventLog\EventLogAction;
use PHPUnit\Framework\TestCase;

class EventLogActionTest extends TestCase
{
    public function test_all_returns_recommended_action_taxonomy(): void
    {
        $this->assertSame([
            'created',
            'updated',
            'deleted',
            'restored',
            'status_changed',
            'corrected',
            'synchronized',
            'imported',
        ], EventLogAction::all());
    }

    public function test_defaults_to_updated_action_uses_taxonomy_constant(): void
    {
        $event = new class
        {
            use DefaultsToUpdatedAction;
        };

        $this->assertSame(EventLogAction::UPDATED, $event->getAction());
    }
}
