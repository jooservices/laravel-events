<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventSourcing;

final class EventCategory
{
    public const DOMAIN = 'domain';

    public const INTEGRATION = 'integration';

    public const AUDIT = 'audit';

    public const SYSTEM = 'system';

    /** @return list<string> */
    public static function all(): array
    {
        return [
            self::DOMAIN,
            self::INTEGRATION,
            self::AUDIT,
            self::SYSTEM,
        ];
    }
}
