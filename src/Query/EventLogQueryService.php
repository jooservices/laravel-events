<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Query;

use DateTimeInterface;
use Illuminate\Support\Collection;
use JOOservices\LaravelEvents\Data\EventLogData;
use JOOservices\LaravelEvents\EventLog\Models\EventLogEntry;

class EventLogQueryService
{
    /** @var list<string> */
    private const ALLOWED_FILTERS = [
        'entity_type',
        'entity_id',
        'action',
        'user_id',
        'meta.correlation_id',
        'meta.causation_id',
    ];

    public function __construct(private readonly EventLogEntry $model)
    {
    }

    /** @return Collection<int, EventLogData> */
    public function byEntity(string $entityType, string $entityId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['entity_type' => $entityType, 'entity_id' => $entityId]);
    }

    /** @return Collection<int, EventLogData> */
    public function byCorrelationId(string $correlationId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['meta.correlation_id' => $correlationId]);
    }

    /** @return Collection<int, EventLogData> */
    public function byCausationId(string $causationId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['meta.causation_id' => $causationId]);
    }

    /** @return Collection<int, EventLogData> */
    public function between(DateTimeInterface $from, DateTimeInterface $to, int $limit = 50): Collection
    {
        QueryGuard::assertDateRange($from, $to);

        return $this->run($limit, [], $from, $to);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, EventLogData>
     */
    public function latest(int $limit = 50, array $filters = []): Collection
    {
        return $this->run($limit, $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, EventLogData>
     */
    private function run(
        int $limit,
        array $filters = [],
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null,
    ): Collection {
        QueryGuard::assertLimit($limit);
        QueryGuard::assertFilters($filters, self::ALLOWED_FILTERS);

        $query = $this->model->newQuery();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }
        if ($from !== null) {
            $query->where('created_at', '>=', $from);
        }
        if ($to !== null) {
            $query->where('created_at', '<=', $to);
        }

        return $query->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn(EventLogEntry $entry): EventLogData => EventLogData::fromArray($entry->toArray()))
            ->values();
    }
}
