<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Contracts;

/**
 * Optional interface for loggable events that specify the action.
 * When not implemented, the subscriber uses "updated".
 *
 * Recommended taxonomy: created, updated, deleted, restored, status_changed,
 * corrected, synchronized, imported.
 */
interface HasLogAction
{
    public function getAction(): string;
}
