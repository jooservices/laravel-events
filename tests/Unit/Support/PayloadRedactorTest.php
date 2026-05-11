<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use JOOservices\LaravelEvents\Support\PayloadRedactor;
use JOOservices\LaravelEvents\Tests\TestCase;

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

        $profile = $redacted['profile'] ?? null;
        $this->assertIsArray($profile);
        $tokens = $profile['tokens'] ?? null;
        $this->assertIsArray($tokens);
        $this->assertSame('[REDACTED]', $tokens['access_token'] ?? null);
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
