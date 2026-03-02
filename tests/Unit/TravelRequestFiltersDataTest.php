<?php

namespace Tests\Unit;

use App\Enums\TravelRequestStatus;
use App\TravelRequestFiltersData;
use PHPUnit\Framework\TestCase;

class TravelRequestFiltersDataTest extends TestCase
{
    public function test_from_array_maps_all_filters_to_typed_properties(): void
    {
        $dto = TravelRequestFiltersData::fromArray([
            'status' => 'requested',
            'destination' => 'Paulo',
            'created_from' => '2026-03-01',
            'created_to' => '2026-03-30',
            'travel_from' => '2026-04-01',
            'travel_to' => '2026-04-15',
        ]);

        $this->assertSame(TravelRequestStatus::Requested, $dto->status);
        $this->assertSame('Paulo', $dto->destination);
        $this->assertSame('2026-03-01', $dto->createdFrom?->format('Y-m-d'));
        $this->assertSame('2026-03-30', $dto->createdTo?->format('Y-m-d'));
        $this->assertSame('2026-04-01', $dto->travelFrom?->format('Y-m-d'));
        $this->assertSame('2026-04-15', $dto->travelTo?->format('Y-m-d'));
    }

    public function test_from_array_supports_empty_filters(): void
    {
        $dto = TravelRequestFiltersData::fromArray([]);

        $this->assertNull($dto->status);
        $this->assertNull($dto->destination);
        $this->assertNull($dto->createdFrom);
        $this->assertNull($dto->createdTo);
        $this->assertNull($dto->travelFrom);
        $this->assertNull($dto->travelTo);
    }
}
