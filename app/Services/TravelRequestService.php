<?php

namespace App\Services;

use App\DTOs\TravelRequestFiltersData;
use App\Enums\TravelRequestStatus;
use App\Exceptions\InvalidStatusTransition;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TravelRequestStatusUpdatedNotification;
use Illuminate\Database\Eloquent\Collection;

class TravelRequestService
{
    public function list(TravelRequestFiltersData $filters): Collection
    {
        $query = TravelRequest::query()
            ->with('requester')
            ->latest();

        $filters->apply($query);

        return $query->get();
    }

    public function create(User $user, array $data): TravelRequest
    {
        return TravelRequest::query()->create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'destination' => data_get($data, 'destination'),
            'departure_date' => data_get($data, 'departure_date'),
            'return_date' => data_get($data, 'return_date'),
            'status' => TravelRequestStatus::Requested,
        ])->load('requester');
    }

    public function update(TravelRequest $travelRequest, array $data): TravelRequest
    {
        $travelRequest->update($data);

        return $travelRequest->fresh()->load('requester');
    }

    public function delete(TravelRequest $travelRequest): void
    {
        $travelRequest->delete();
    }

    public function updateStatus(TravelRequest $travelRequest, TravelRequestStatus $newStatus): TravelRequest
    {
        if (! $this->canTransitionStatus($travelRequest->status, $newStatus)) {
            throw new InvalidStatusTransition($travelRequest->status->value, $newStatus->value);
        }
            
        $travelRequest->update(['status' => $newStatus]);

        $travelRequest->requester?->notify(new TravelRequestStatusUpdatedNotification($travelRequest));

        return $travelRequest->fresh()->load('requester');
    }

    public function canView(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_admin || $travelRequest->requester_id === $user->id;
    }

    public function canUpdate(User $user, TravelRequest $travelRequest): bool
    {
        return $this->canView($user, $travelRequest);
    }

    public function canDelete(User $user, TravelRequest $travelRequest): bool
    {
        return $this->canView($user, $travelRequest);
    }

    public function canUpdateStatus(User $user, TravelRequest $travelRequest): bool
    {
        return $user->is_admin && $travelRequest->requester_id !== $user->id;
    }

    public function canTransitionStatus(TravelRequestStatus $from, TravelRequestStatus $to): bool
    {
        if ($from === $to) {
            return false;
        }

        return match ($from) {
            TravelRequestStatus::Requested => in_array($to, [
                TravelRequestStatus::Approved,
                TravelRequestStatus::Cancelled,
            ], true),
            TravelRequestStatus::Approved => false,
            TravelRequestStatus::Cancelled => false,
        };
    }
}
