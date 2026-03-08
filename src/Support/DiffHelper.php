<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Support;

class DiffHelper
{
    /**
     * Build per-field diff: [ 'field' => ['old' => x, 'new' => y] ].
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
