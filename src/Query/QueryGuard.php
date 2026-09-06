<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Query;

use DateTimeInterface;
use JOOservices\LaravelEvents\Exceptions\InvalidQueryException;

final class QueryGuard
{
    public static function assertLimit(int $limit): void
    {
        if ($limit < 1 || $limit > 500) {
            throw InvalidQueryException::limitOutOfRange($limit);
        }
    }

    public static function assertDateRange(DateTimeInterface $from, DateTimeInterface $to): void
    {
        if ($from > $to) {
            throw InvalidQueryException::invalidDateRange($from, $to);
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @param  list<string>  $allowedKeys
     */
    public static function assertFilters(array $filters, array $allowedKeys): void
    {
        $allowed = array_fill_keys($allowedKeys, true);

        foreach (array_keys($filters) as $key) {
            if (! isset($allowed[$key])) {
                throw InvalidQueryException::disallowedFilter($key);
            }
        }
    }
}
