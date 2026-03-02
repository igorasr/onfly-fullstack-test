<?php

namespace App;

use App\Enums\TravelRequestStatus;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class TravelRequestFiltersData
{
    public function __construct(
        public readonly ?TravelRequestStatus $status,
        public readonly ?string $destination,
        public readonly ?CarbonImmutable $createdFrom,
        public readonly ?CarbonImmutable $createdTo,
        public readonly ?CarbonImmutable $travelFrom,
        public readonly ?CarbonImmutable $travelTo,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            status: isset($data['status']) ? TravelRequestStatus::from($data['status']) : null,
            destination: data_get($data, 'destination'),
            createdFrom: isset($data['created_from']) ? CarbonImmutable::parse($data['created_from']) : null,
            createdTo: isset($data['created_to']) ? CarbonImmutable::parse($data['created_to']) : null,
            travelFrom: isset($data['travel_from']) ? CarbonImmutable::parse($data['travel_from']) : null,
            travelTo: isset($data['travel_to']) ? CarbonImmutable::parse($data['travel_to']) : null,
        );
    }

    public function apply(Builder $query): void
    {
        if ($this->status !== null) {
            $query->where('status', $this->status->value);
        }

        if ($this->destination !== null) {
            $query->where('destination', 'like', '%'.$this->destination.'%');
        }

        if ($this->createdFrom !== null) {
            $query->whereDate('created_at', '>=', $this->createdFrom->format('Y-m-d'));
        }

        if ($this->createdTo !== null) {
            $query->whereDate('created_at', '<=', $this->createdTo->format('Y-m-d'));
        }

        if ($this->travelFrom !== null) {
            $query->whereDate('return_date', '>=', $this->travelFrom->format('Y-m-d'));
        }

        if ($this->travelTo !== null) {
            $query->whereDate('departure_date', '<=', $this->travelTo->format('Y-m-d'));
        }
    }
}
