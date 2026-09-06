<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventLog;

enum EventLogAction: string
{
    case CREATED = 'created';
    case UPDATED = 'updated';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case STATUS_CHANGED = 'status_changed';
    case CORRECTED = 'corrected';
    case SYNCHRONIZED = 'synchronized';
    case IMPORTED = 'imported';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_map(
            static fn(self $action): string => $action->value,
            self::cases(),
        );
    }
}
