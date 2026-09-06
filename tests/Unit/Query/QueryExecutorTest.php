<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Query;

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
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with('event_name', 'OrderPlaced')
            ->andReturnSelf();

        QueryExecutor::applyFilters($query, ['event_name' => 'OrderPlaced']);
        $this->addToAssertionCount(1);
    }

    public function test_apply_filters_ors_dual_path_correlation_keys(): void
    {
        $query = Mockery::mock(Builder::class);
        $query->shouldReceive('where')
            ->once()
            ->with(Mockery::on(static function (mixed $callback): bool {
                if (! is_callable($callback)) {
                    return false;
                }

                $nested = Mockery::mock(Builder::class);
                $nested->shouldReceive('where')
                    ->once()
                    ->with('correlation_id', 'corr-1')
                    ->andReturnSelf();
                $nested->shouldReceive('orWhere')
                    ->once()
                    ->with('metadata.correlation_id', 'corr-1')
                    ->andReturnSelf();

                $callback($nested);

                return true;
            }))
            ->andReturnSelf();

        QueryExecutor::applyFilters(
            $query,
            ['correlation_id' => 'corr-1'],
            QueryExecutor::STORED_EVENT_DUAL_PATHS,
        );
        $this->addToAssertionCount(1);
    }
}
