<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Support;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use JOOservices\LaravelEvents\Exceptions\InvalidEventDataException;
use Throwable;

/**
 * Normalize storage / Eloquent toArray() date values into DateTimeInterface.
 */
final class DateTimeParser
{
    public static function optional(mixed $value, string $field): ?DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        if (is_int($value)) {
            return (new DateTimeImmutable('@' . $value))->setTimezone(new DateTimeZone('UTC'));
        }

        if (is_string($value)) {
            return self::fromString($value, $field);
        }

        return self::fromConvertibleObject($value, $field);
    }

    private static function fromString(string $value, string $field): DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($value);
        } catch (Throwable) {
            throw self::invalid($field);
        }
    }

    private static function fromConvertibleObject(mixed $value, string $field): DateTimeImmutable
    {
        if (is_object($value) && is_callable([$value, 'toDateTime'])) {
            $converted = $value->toDateTime();
            if ($converted instanceof DateTimeInterface) {
                return DateTimeImmutable::createFromInterface($converted);
            }
        }

        throw self::invalid($field);
    }

    private static function invalid(string $field): InvalidEventDataException
    {
        return InvalidEventDataException::invalidType(
            $field,
            'a DateTimeInterface, parseable datetime string, UTCDateTime, or null',
        );
    }
}
