<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\EventLog\Contracts;

/**
 * Optional interface for loggable events that specify the action.
 * When not implemented, the subscriber uses "updated".
 *
 * Use: created, updated, deleted, restored
 */
interface HasLogAction
{
    public function getAction(): string;
}
