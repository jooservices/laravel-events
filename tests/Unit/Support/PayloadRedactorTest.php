<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Support\PayloadRedactor;
use JOOservices\LaravelEvents\Tests\TestCase;

class PayloadRedactorTest extends TestCase
{
    public function test_redacts_simple_key(): void
    {
        $faker = FakerFactory::create();
        $name = $faker->firstName();
        $secret = $faker->password();

        $redacted = (new PayloadRedactor())->redact(['password' => $secret, 'name' => $name]);

        self::assertSame(['password' => '[REDACTED]', 'name' => $name], $redacted);
    }

    public function test_redacts_nested_key(): void
    {
        $faker = FakerFactory::create();
        $token = $faker->sha256();

        $redacted = (new PayloadRedactor())->redact([
            'profile' => [
                'tokens' => [
                    'access_token' => $token,
                ],
            ],
        ]);

        $profile = $redacted['profile'] ?? null;
        self::assertIsArray($profile);
        $tokens = $profile['tokens'] ?? null;
        self::assertIsArray($tokens);
        self::assertSame('[REDACTED]', $tokens['access_token'] ?? null);
    }

    public function test_redaction_keys_are_case_insensitive(): void
    {
        $faker = FakerFactory::create();
        $token = 'Bearer ' . $faker->sha256();

        $redacted = (new PayloadRedactor())->redact(['Authorization' => $token]);

        self::assertSame('[REDACTED]', $redacted['Authorization']);
    }

    public function test_redaction_can_be_disabled(): void
    {
        $faker = FakerFactory::create();
        $secret = $faker->password();
        config()->set('events.redaction.enabled', false);

        $redacted = (new PayloadRedactor())->redact(['password' => $secret]);

        self::assertSame(['password' => $secret], $redacted);
    }
}
