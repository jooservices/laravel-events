<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Support;

class DiffHelper
{
    /**
     * @param  array<string, mixed>  $prev
     * @param  array<string, mixed>  $current
     * @return array<string, array{old: mixed, new: mixed}>
     *                                                      Build per-field diff.
     */
    public function diff(array $prev, array $current): array
    {
        $diff = [];

        foreach ($current as $key => $new) {
            $old = $prev[$key] ?? null;
            if ($old !== $new) {
                $diff[$key] = ['old' => $old, 'new' => $new];
            }
        }

        return $diff;
    }
}
