<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Query;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Eloquent\Builder;
use JOOservices\LaravelEvents\Query\QueryExecutor;
use Mockery;
use PHPUnit\Framework\TestCase;

final class QueryExecutorTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_apply_filters_uses_plain_where_for_single_path_keys(): void
    {
        $faker = FakerFactory::create();
        $eventName = $faker->slug(2);

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with('event_name', $eventName)
            ->andReturnSelf();

        QueryExecutor::applyFilters($query, ['event_name' => $eventName]);
        $this->addToAssertionCount(1);
    }

    public function test_apply_filters_ors_dual_path_correlation_keys(): void
    {
        $faker = FakerFactory::create();
        $correlationId = $faker->uuid();

        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with(Mockery::on(static function (mixed $callback) use ($correlationId): bool {
                if (! is_callable($callback)) {
                    return false;
                }

                $nested = Mockery::mock(Builder::class);
                $nested->shouldReceive('where')
                    ->once()
                    ->with('correlation_id', $correlationId)
                    ->andReturnSelf();
                $nested->shouldReceive('orWhere')
                    ->once()
                    ->with('metadata.correlation_id', $correlationId)
                    ->andReturnSelf();

                $callback($nested);

                return true;
            }))
            ->andReturnSelf();

        QueryExecutor::applyFilters(
            $query,
            ['correlation_id' => $correlationId],
            QueryExecutor::STORED_EVENT_DUAL_PATHS,
        );
        $this->addToAssertionCount(1);
    }
}
