<?php

declare(strict_types=1);

namespace JooServices\LaravelEvents\Tests\Unit\Support;

use JooServices\LaravelEvents\Support\PayloadRedactor;
use JooServices\LaravelEvents\Tests\TestCase;

class PayloadRedactorTest extends TestCase
{
    public function test_redacts_simple_key(): void
    {
        $redacted = (new PayloadRedactor)->redact(['password' => 'secret', 'name' => 'Jane']);

        $this->assertSame(['password' => '[REDACTED]', 'name' => 'Jane'], $redacted);
    }

    public function test_redacts_nested_key(): void
    {
        $redacted = (new PayloadRedactor)->redact([
            'profile' => [
                'tokens' => [
                    'access_token' => 'abc',
                ],
            ],
        ]);

        $this->assertSame('[REDACTED]', $redacted['profile']['tokens']['access_token']);
    }

    public function test_redaction_keys_are_case_insensitive(): void
    {
        $redacted = (new PayloadRedactor)->redact(['Authorization' => 'Bearer token']);

        $this->assertSame('[REDACTED]', $redacted['Authorization']);
    }

    public function test_redaction_can_be_disabled(): void
    {
        config()->set('events.redaction.enabled', false);

        $redacted = (new PayloadRedactor)->redact(['password' => 'secret']);

        $this->assertSame(['password' => 'secret'], $redacted);
    }
}
