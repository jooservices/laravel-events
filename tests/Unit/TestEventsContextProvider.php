<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit;

final class TestEventsContextProvider
{
    /** @return array<string, mixed> */
    public function __invoke(): array
    {
        return [
            'user_id' => 'from-provider',
            'source' => 'test',
        ];
    }
}
