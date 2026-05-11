<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventLog;

final class EventLogAction
{
    public const CREATED = 'created';

    public const UPDATED = 'updated';

    public const DELETED = 'deleted';

    public const RESTORED = 'restored';

    public const STATUS_CHANGED = 'status_changed';

    public const CORRECTED = 'corrected';

    public const SYNCHRONIZED = 'synchronized';

    public const IMPORTED = 'imported';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::CREATED,
            self::UPDATED,
            self::DELETED,
            self::RESTORED,
            self::STATUS_CHANGED,
            self::CORRECTED,
            self::SYNCHRONIZED,
            self::IMPORTED,
        ];
    }
}
