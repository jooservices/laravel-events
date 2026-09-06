<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use Faker\Factory as FakerFactory;
use JOOservices\LaravelEvents\Support\EventMetadata;
use PHPUnit\Framework\TestCase;

class EventMetadataTest extends TestCase
{
    public function test_trace_metadata_omits_null_values(): void
    {
        $faker = FakerFactory::create();
        $requestId = $faker->uuid();
        $causationId = $faker->uuid();

        self::assertSame([
            'request_id' => $requestId,
            'causation_id' => $causationId,
        ], EventMetadata::trace($requestId, null, $causationId));
    }

    public function test_version_metadata_accepts_int_or_string_values(): void
    {
        $faker = FakerFactory::create();
        $schema = $faker->numberBetween(1, 5);
        $eventVersion = $faker->year() . '-' . $faker->month();

        self::assertSame([
            'schema_version' => $schema,
            'event_version' => $eventVersion,
        ], EventMetadata::version($schema, $eventVersion));
    }

    public function test_correction_metadata_uses_documented_keys(): void
    {
        $faker = FakerFactory::create();
        $supersedes = $faker->uuid();
        $reason = $faker->slug(2);

        self::assertSame([
            'supersedes_event_id' => $supersedes,
            'correction_reason' => $reason,
        ], EventMetadata::correction(null, $supersedes, null, $reason));
    }

    public function test_merge_combines_metadata_and_removes_null_values(): void
    {
        $faker = FakerFactory::create();
        $requestId = $faker->uuid();
        $source = $faker->slug(2);
        $tenantId = $faker->uuid();

        self::assertSame([
            'request_id' => $requestId,
            'source' => $source,
            'tenant_id' => $tenantId,
        ], EventMetadata::merge(
            EventMetadata::trace($requestId),
            EventMetadata::source($source),
            EventMetadata::tenant($tenantId),
            ['ignored' => null],
        ));
    }

    public function test_make_builds_optional_metadata(): void
    {
        $faker = FakerFactory::create();
        $correlationId = $faker->uuid();
        $causationId = $faker->uuid();
        $requestId = $faker->uuid();
        $source = $faker->slug();
        $channel = $faker->slug();
        $tenantId = $faker->uuid();

        $metadata = EventMetadata::make()
            ->correlationId($correlationId)
            ->causationId($causationId)
            ->requestId($requestId)
            ->source($source, $channel)
            ->eventCategory('domain')
            ->schemaVersion(1)
            ->eventVersion(2)
            ->tenantId($tenantId)
            ->toArray();

        self::assertSame([
            'correlation_id' => $correlationId,
            'causation_id' => $causationId,
            'request_id' => $requestId,
            'source' => $source,
            'channel' => $channel,
            'event_category' => 'domain',
            'schema_version' => 1,
            'event_version' => 2,
            'tenant_id' => $tenantId,
        ], $metadata);
    }

    public function test_category_metadata_omits_null_values(): void
    {
        self::assertSame(['event_category' => 'integration'], EventMetadata::category('integration'));
        self::assertSame([], EventMetadata::category());
    }

    public function test_builder_source_without_channel_clears_previous_channel(): void
    {
        $faker = FakerFactory::create();
        $source = $faker->slug();

        $metadata = EventMetadata::make()
            ->source($source, 'api')
            ->source($source)
            ->toArray();

        self::assertSame(['source' => $source], $metadata);
    }
}
