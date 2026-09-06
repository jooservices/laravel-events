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
        self::assertNull(DateTimeParser::optional(null, 'created_at'));
        self::assertNull(DateTimeParser::optional('', 'created_at'));
    }

    public function test_optional_preserves_datetime_interface(): void
    {
        $value = new DateTimeImmutable('2026-05-01T12:00:00Z');

        self::assertSame($value, DateTimeParser::optional($value, 'created_at'));
    }

    public function test_optional_parses_eloquent_iso_string(): void
    {
        $faker = FakerFactory::create();
        $iso = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d\TH:i:s.u\Z');

        $parsed = DateTimeParser::optional($iso, 'created_at');

        self::assertInstanceOf(DateTimeImmutable::class, $parsed);
        self::assertSame(
            (new DateTimeImmutable($iso))->format(DateTimeImmutable::ATOM),
            $parsed->format(DateTimeImmutable::ATOM),
        );
    }

    public function test_optional_parses_unix_timestamp(): void
    {
        $parsed = DateTimeParser::optional(1_714_564_800, 'occurred_at');

        self::assertInstanceOf(DateTimeImmutable::class, $parsed);
        self::assertSame(1_714_564_800, $parsed->getTimestamp());
    }

    public function test_optional_parses_object_with_to_date_time(): void
    {
        $source = new DateTimeImmutable('2026-06-01T00:00:00Z');
        $value = new class ($source) {
            public function __construct(private DateTimeImmutable $inner)
            {
            }

            public function toDateTime(): DateTimeImmutable
            {
                return $this->inner;
            }
        };

        $parsed = DateTimeParser::optional($value, 'created_at');

        self::assertInstanceOf(DateTimeImmutable::class, $parsed);
        self::assertSame($source->getTimestamp(), $parsed->getTimestamp());
    }

    public function test_optional_rejects_unparseable_values(): void
    {
        self::expectException(InvalidEventDataException::class);

        DateTimeParser::optional(['not' => 'a date'], 'created_at');
    }

    public function test_optional_rejects_object_without_usable_to_date_time(): void
    {
        self::expectException(InvalidEventDataException::class);

        DateTimeParser::optional(new class {
            public function toDateTime(): string
            {
                return 'nope';
            }
        }, 'created_at');
    }
}
