<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Support;

class DiffHelper
{
    /**
     * Build per-field diff including additions, changes, and removals.
     *
     * Removed keys (present in `$prev`, absent from `$current`) are recorded as
     * `['old' => $previousValue, 'new' => null]`.
     *
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $current
     * @return array<string, array{old: mixed, new: mixed}>
     */
    public function diff(array $prev, array $current): array
    {
        $diff = [];

        foreach ($current as $key => $new) {
            $old = array_key_exists($key, $prev) ? $prev[$key] : null;
            if ($old !== $new) {
                $diff[$key] = ['old' => $old, 'new' => $new];
            }
        }

        foreach ($prev as $key => $old) {
            if (! array_key_exists($key, $current)) {
                $diff[$key] = ['old' => $old, 'new' => null];
            }
        }

        return $diff;
    }
}
