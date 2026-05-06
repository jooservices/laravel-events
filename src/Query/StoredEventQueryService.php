<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Query;

use DateTimeInterface;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use JooServices\LaravelEvents\Data\StoredEventData;
use JooServices\LaravelEvents\EventSourcing\Models\StoredEvent;

class StoredEventQueryService
{
    public function __construct(private readonly StoredEvent $model) {}

    /** @return Collection<int, StoredEventData> */
    public function byAggregate(string $aggregateType, string $aggregateId, int $limit = 50): Collection
    {
        return $this->latest($limit, ['aggregate_id' => $aggregateId]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byEventName(string $eventName, int $limit = 50): Collection
    {
        return $this->latest($limit, ['event_class' => $eventName]);
    }

    /** @return Collection<int, StoredEventData> */
    public function byCorrelationId(string $correlationId, int $limit = 50): Collection
    {
        $this->assertLimit($limit);

        return $this->latest(500)
            ->filter(
                fn (StoredEventData $event): bool => ($event->metadata['correlation_id'] ?? null) === $correlationId
            )
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, StoredEventData> */
    public function byCausationId(string $causationId, int $limit = 50): Collection
    {
        $this->assertLimit($limit);

        return $this->latest(500)
            ->filter(
                fn (StoredEventData $event): bool => ($event->metadata['causation_id'] ?? null) === $causationId
            )
            ->take($limit)
            ->values();
    }

    /** @return Collection<int, StoredEventData> */
    public function between(DateTimeInterface $from, DateTimeInterface $to, int $limit = 50): Collection
    {
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
        $this->assertLimit($limit);

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
            ->map(fn (StoredEvent $event): StoredEventData => StoredEventData::fromArray($event->toArray()))
            ->values();
    }

    private function assertLimit(int $limit): void
    {
        if ($limit < 1 || $limit > 500) {
            throw new InvalidArgumentException('Query limit must be between 1 and 500.');
        }
    }
}
