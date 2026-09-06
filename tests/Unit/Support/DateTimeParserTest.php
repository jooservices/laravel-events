<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use DateTimeImmutable;
use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;
use JOOservices\LaravelEvents\Support\DateTimeParser;
use PHPUnit\Framework\TestCase;

final class DateTimeParserTest extends TestCase
{
    public function test_optional_returns_null_for_empty_values(): void
    {
        $this->assertNull(DateTimeParser::optional(null, 'created_at'));
        $this->assertNull(DateTimeParser::optional('', 'created_at'));
    }

    public function test_optional_preserves_datetime_interface(): void
    {
        $value = new DateTimeImmutable('2026-05-01T12:00:00Z');

        $this->assertSame($value, DateTimeParser::optional($value, 'created_at'));
    }

    public function test_optional_parses_eloquent_iso_string(): void
    {
        $faker = FakerFactory::create();
        $iso = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d\TH:i:s.u\Z');

        $parsed = DateTimeParser::optional($iso, 'created_at');

        $this->assertInstanceOf(DateTimeImmutable::class, $parsed);
        $this->assertSame(
            (new DateTimeImmutable($iso))->format(DateTimeImmutable::ATOM),
            $parsed->format(DateTimeImmutable::ATOM),
        );
    }

    public function test_optional_parses_unix_timestamp(): void
    {
        $parsed = DateTimeParser::optional(1_714_564_800, 'occurred_at');

        $this->assertInstanceOf(DateTimeImmutable::class, $parsed);
        $this->assertSame(1_714_564_800, $parsed->getTimestamp());
    }

    public function test_optional_rejects_unparseable_values(): void
    {
        $this->expectException(InvalidEventDataException::class);

        DateTimeParser::optional(['not' => 'a date'], 'created_at');
    }
}
