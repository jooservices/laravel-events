<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Query;

use DateTimeInterface;
use Illuminate\Support\Collection;
use JOOservices\LaravelEvents\Data\StoredEventData;
use JOOservices\LaravelEvents\EventSourcing\Models\StoredEvent;

class StoredEventQueryService
{
    /** @var list<string> */
    private const ALLOWED_FILTERS = [
        'aggregate_id',
        'event_class',
        'event_name',
        'event_category',
        'event_id',
        'user_id',
        'correlation_id',
        'causation_id',
        'metadata.correlation_id',
        'metadata.causation_id',
    ];

    public function __construct(private readonly StoredEvent $model)
    {
    }

    /** @return Collection<int, StoredEventData> */
    public function byAggregateId(string $aggregateId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['aggregate_id' => $aggregateId]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byEventName(string $eventName, int $limit = 50): Collection
    {
        return $this->latest($limit, ['event_name' => $eventName]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byEventClass(string $eventClass, int $limit = 50): Collection
    {
        return $this->latest($limit, ['event_class' => $eventClass]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byEventCategory(string $eventCategory, int $limit = 50): Collection
    {
        return $this->latest($limit, ['event_category' => $eventCategory]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byEventId(string $eventId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['event_id' => $eventId]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byCorrelationId(string $correlationId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['metadata.correlation_id' => $correlationId]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byCausationId(string $causationId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['metadata.causation_id' => $causationId]);
    }

    /** @return Collection<int, StoredEventData> */
    public function between(DateTimeInterface $from, DateTimeInterface $to, int $limit = 50): Collection
    {
        QueryGuard::assertDateRange($from, $to);

        return $this->run($limit, [], $from, $to);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, StoredEventData>
     */
    public function latest(int $limit = 50, array $filters = []): Collection
    {
        return $this->run($limit, $filters);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Collection<int, StoredEventData>
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
            ->map(fn(StoredEvent $event): StoredEventData => StoredEventData::fromArray($event->toArray()))
            ->values();
    }
}
