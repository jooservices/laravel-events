<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Concerns;

/**
 * Trait providing default getAction() for HasLogAction (returns "updated").
 * Use when your log event always represents an update; override getAction() for created/deleted/restored.
 */
trait DefaultsToUpdatedAction
{
    public function getAction(): string
    {
        return 'updated';
    }
}
