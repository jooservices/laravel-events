<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\Support;

use JooServices\LaravelEvents\Support\DiffHelper;
use PHPUnit\Framework\TestCase;

class DiffHelperTest extends TestCase
{
    private DiffHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new DiffHelper;
    }

    public function test_diff_returns_empty_when_arrays_are_identical(): void
    {
        $prev = ['a' => 1, 'b' => 2];
        $current = ['a' => 1, 'b' => 2];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame([], $result);
    }

    public function test_diff_returns_changed_fields_with_old_and_new(): void
    {
        $prev = ['a' => 1, 'b' => 2, 'c' => 3];
        $current = ['a' => 1, 'b' => 99, 'c' => 3];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame(['b' => ['old' => 2, 'new' => 99]], $result);
    }

    public function test_diff_includes_new_keys_from_current(): void
    {
        $prev = ['a' => 1];
        $current = ['a' => 1, 'b' => 2];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame(['b' => ['old' => null, 'new' => 2]], $result);
    }

    public function test_diff_handles_multiple_changes(): void
    {
        $prev = ['name' => 'Old', 'count' => 0];
        $current = ['name' => 'New', 'count' => 10];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame([
            'name' => ['old' => 'Old', 'new' => 'New'],
            'count' => ['old' => 0, 'new' => 10],
        ], $result);
    }

    public function test_diff_handles_empty_prev(): void
    {
        $prev = [];
        $current = ['a' => 1, 'b' => 2];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame([
            'a' => ['old' => null, 'new' => 1],
            'b' => ['old' => null, 'new' => 2],
        ], $result);
    }

    public function test_diff_handles_empty_current(): void
    {
        $prev = ['a' => 1, 'b' => 2];
        $current = [];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame([], $result);
    }

    public function test_diff_treats_null_in_current_as_explicit_changed_value(): void
    {
        $prev = ['name' => 'Old'];
        $current = ['name' => null];

        $result = $this->helper->diff($prev, $current);

        $this->assertSame(['name' => ['old' => 'Old', 'new' => null]], $result);
    }
}
