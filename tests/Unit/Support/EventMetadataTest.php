<?php

declare(strict_types=1);

namespace JOOservices\LaravelEvents\Tests\Unit\Support;

use JOOservices\LaravelEvents\Support\EventMetadata;
use PHPUnit\Framework\TestCase;

class EventMetadataTest extends TestCase
{
    public function test_trace_metadata_omits_null_values(): void
    {
        $this->assertSame([
            'request_id' => 'req-1',
            'causation_id' => 'event-1',
        ], EventMetadata::trace('req-1', null, 'event-1'));
    }

    public function test_version_metadata_accepts_int_or_string_values(): void
    {
        $this->assertSame([
            'schema_version' => 2,
            'event_version' => '2026-04',
        ], EventMetadata::version(2, '2026-04'));
    }

    public function test_correction_metadata_uses_documented_keys(): void
    {
        $this->assertSame([
            'supersedes_event_id' => 'stored-1',
            'correction_reason' => 'customer_request',
        ], EventMetadata::correction(null, 'stored-1', null, 'customer_request'));
    }

    public function test_merge_combines_metadata_and_removes_null_values(): void
    {
        $this->assertSame([
            'request_id' => 'req-1',
            'source' => 'orders-service',
            'tenant_id' => 'tenant-1',
        ], EventMetadata::merge(
            EventMetadata::trace('req-1'),
            EventMetadata::source('orders-service'),
            EventMetadata::tenant('tenant-1'),
            ['ignored' => null],
        ));
    }

    public function test_make_builds_optional_metadata(): void
    {
        $metadata = EventMetadata::make()
            ->correlationId('corr-1')
            ->causationId('cmd-1')
            ->requestId('req-1')
            ->source('orders', 'api')
            ->eventCategory('domain')
            ->schemaVersion(1)
            ->eventVersion(2)
            ->tenantId('tenant-1')
            ->toArray();

        $this->assertSame([
            'correlation_id' => 'corr-1',
            'causation_id' => 'cmd-1',
            'request_id' => 'req-1',
            'source' => 'orders',
            'channel' => 'api',
            'event_category' => 'domain',
            'schema_version' => 1,
            'event_version' => 2,
            'tenant_id' => 'tenant-1',
        ], $metadata);
    }

    public function test_category_metadata_omits_null_values(): void
    {
        $this->assertSame(['event_category' => 'integration'], EventMetadata::category('integration'));
        $this->assertSame([], EventMetadata::category());
    }

    public function test_builder_source_without_channel_clears_previous_channel(): void
    {
        $metadata = EventMetadata::make()
            ->source('orders', 'api')
            ->source('orders')
            ->toArray();

        $this->assertSame(['source' => 'orders'], $metadata);
    }
}
