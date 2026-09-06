<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Query;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\Data\StoredEventData;

/**
 * Shared filter application and DTO mapping for Mongo query services.
 */
final class QueryExecutor
{
    /** @var array<string, string> */
    public const STORED_EVENT_DUAL_PATHS = [
        'correlation_id' => 'metadata.correlation_id',
        'causation_id' => 'metadata.causation_id',
        'metadata.correlation_id' => 'correlation_id',
        'metadata.causation_id' => 'causation_id',
    ];

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @param  array<string, mixed>  $filters
     * @param  array<string, string>  $dualPaths
     */
    public static function applyFilters(Builder $query, array $filters, array $dualPaths = []): void
    {
        foreach ($filters as $key => $value) {
            if (isset($dualPaths[$key])) {
                $alternate = $dualPaths[$key];
                $query->where(static function (Builder $nested) use ($key, $alternate, $value): void {
                    $nested->where($key, $value)->orWhere($alternate, $value);
                });

                continue;
            }

            $query->where($key, $value);
        }
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return Collection<int, StoredEventData>
     */
    public static function fetchStoredEvents(
        Builder $query,
        int $limit,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
    ): Collection {
        self::applyCreatedAtRange($query, $from, $to);

        /** @var Collection<int, StoredEventData> $mapped */
        $mapped = $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(static fn($event): StoredEventData => StoredEventData::fromArray($event->toArray()))
            ->values();

        return $mapped;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     * @return Collection<int, EventLogData>
     */
    public static function fetchEventLogs(
        Builder $query,
        int $limit,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
    ): Collection {
        self::applyCreatedAtRange($query, $from, $to);

        /** @var Collection<int, EventLogData> $mapped */
        $mapped = $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(static fn($entry): EventLogData => EventLogData::fromArray($entry->toArray()))
            ->values();

        return $mapped;
    }

    /**
     * @param  Builder<\Illuminate\Database\Eloquent\Model>  $query
     */
    private static function applyCreatedAtRange(
        Builder $query,
        ?DateTimeInterface $from,
        ?DateTimeInterface $to,
    ): void {
        if ($from !== null) {
            $query->where('created_at', '>=', $from);
        }
        if ($to !== null) {
            $query->where('created_at', '<=', $to);
        }
    }
}
