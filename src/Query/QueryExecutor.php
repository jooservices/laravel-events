<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Query;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * @template TModel of Model
     * @param  Builder<TModel>  $query
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
     * @template TModel of Model
     * @param  Builder<TModel>  $query
     * @return Collection<int, StoredEventData>
     */
    public static function fetchStoredEvents(
        Builder $query,
        int $limit,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
    ): Collection {
        self::applyCreatedAtRange($query, $from, $to);

        $rows = $query->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        /** @var Collection<int, StoredEventData> $mapped */
        $mapped = $rows
            ->map(static function (Model $event): StoredEventData {
                /** @var array<string, mixed> $attributes */
                $attributes = [];
                foreach ($event->toArray() as $key => $value) {
                    if (is_string($key)) {
                        $attributes[$key] = $value;
                    }
                }

                return StoredEventData::fromArray($attributes);
            })
            ->values();

        return $mapped;
    }

    /**
     * @template TModel of Model
     * @param  Builder<TModel>  $query
     * @return Collection<int, EventLogData>
     */
    public static function fetchEventLogs(
        Builder $query,
        int $limit,
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
    ): Collection {
        self::applyCreatedAtRange($query, $from, $to);

        $rows = $query->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();

        /** @var Collection<int, EventLogData> $mapped */
        $mapped = $rows
            ->map(static function (Model $entry): EventLogData {
                /** @var array<string, mixed> $attributes */
                $attributes = [];
                foreach ($entry->toArray() as $key => $value) {
                    if (is_string($key)) {
                        $attributes[$key] = $value;
                    }
                }

                return EventLogData::fromArray($attributes);
            })
            ->values();

        return $mapped;
    }

    /**
     * @template TModel of Model
     * @param  Builder<TModel>  $query
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
