<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\EventSourcing;

enum EventCategory: string
{
    case DOMAIN = 'domain';
    case INTEGRATION = 'integration';
    case AUDIT = 'audit';
    case SYSTEM = 'system';

    /** @return list<string> */
    public static function all(): array
    {
        return array_map(
            static fn(self $category): string => $category->value,
            self::cases(),
        );
    }
}
