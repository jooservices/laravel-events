<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Support\DiffHelper;
use PHPUnit\Framework\TestCase;

class DiffHelperTest extends TestCase
{
    private DiffHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new DiffHelper();
    }

    public function test_diff_returns_empty_when_arrays_are_identical(): void
    {
        $faker = FakerFactory::create();
        $key = $faker->unique()->word();
        $value = $faker->numberBetween(1, 100);
        $prev = [$key => $value, 'stable' => 2];
        $current = [$key => $value, 'stable' => 2];

        self::assertSame([], $this->helper->diff($prev, $current));
    }

    public function test_diff_returns_changed_fields_with_old_and_new(): void
    {
        $faker = FakerFactory::create();
        $field = $faker->unique()->word();
        $old = $faker->numberBetween(1, 50);
        $new = $old + $faker->numberBetween(1, 50);

        $result = $this->helper->diff(
            ['a' => 1, $field => $old, 'c' => 3],
            ['a' => 1, $field => $new, 'c' => 3],
        );

        self::assertSame([$field => ['old' => $old, 'new' => $new]], $result);
    }

    public function test_diff_includes_new_keys_from_current(): void
    {
        $faker = FakerFactory::create();
        $field = $faker->unique()->word();
        $value = $faker->numberBetween(1, 100);

        $result = $this->helper->diff(['a' => 1], ['a' => 1, $field => $value]);

        self::assertSame([$field => ['old' => null, 'new' => $value]], $result);
    }

    public function test_diff_handles_multiple_changes(): void
    {
        $faker = FakerFactory::create();
        $oldName = $faker->unique()->word();
        $newName = $faker->unique()->word();
        $oldCount = $faker->numberBetween(0, 5);
        $newCount = $faker->numberBetween(6, 20);

        $result = $this->helper->diff(
            ['name' => $oldName, 'count' => $oldCount],
            ['name' => $newName, 'count' => $newCount],
        );

        self::assertSame([
            'name' => ['old' => $oldName, 'new' => $newName],
            'count' => ['old' => $oldCount, 'new' => $newCount],
        ], $result);
    }

    public function test_diff_handles_empty_prev(): void
    {
        $faker = FakerFactory::create();
        $a = $faker->numberBetween(1, 10);
        $b = $faker->numberBetween(11, 20);

        $result = $this->helper->diff([], ['a' => $a, 'b' => $b]);

        self::assertSame([
            'a' => ['old' => null, 'new' => $a],
            'b' => ['old' => null, 'new' => $b],
        ], $result);
    }

    public function test_diff_handles_empty_current_as_removals(): void
    {
        $faker = FakerFactory::create();
        $a = $faker->numberBetween(1, 10);
        $b = $faker->numberBetween(11, 20);

        $result = $this->helper->diff(['a' => $a, 'b' => $b], []);

        self::assertSame([
            'a' => ['old' => $a, 'new' => null],
            'b' => ['old' => $b, 'new' => null],
        ], $result);
    }

    public function test_diff_includes_removed_keys_from_prev(): void
    {
        $faker = FakerFactory::create();
        $removed = $faker->numberBetween(1, 10);
        $changedOld = $faker->numberBetween(11, 20);
        $changedNew = $faker->numberBetween(21, 30);

        $result = $this->helper->diff(
            ['a' => 1, 'b' => $removed, 'c' => $changedOld],
            ['a' => 1, 'c' => $changedNew],
        );

        self::assertSame([
            'c' => ['old' => $changedOld, 'new' => $changedNew],
            'b' => ['old' => $removed, 'new' => null],
        ], $result);
    }

    public function test_diff_treats_null_in_current_as_explicit_changed_value(): void
    {
        $faker = FakerFactory::create();
        $old = $faker->word();

        $result = $this->helper->diff(['name' => $old], ['name' => null]);

        self::assertSame(['name' => ['old' => $old, 'new' => null]], $result);
    }
}
