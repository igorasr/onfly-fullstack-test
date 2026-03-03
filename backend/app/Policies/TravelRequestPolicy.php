<?php

namespace App\Policies;

use App\Models\TravelRequest;
use App\Models\User;
use App\Services\TravelRequestService;

class TravelRequestPolicy
{
    public function __construct(private TravelRequestService $travelRequestService) {}

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TravelRequest $travelRequest): bool
    {
        return $this->travelRequestService->canView($user, $travelRequest);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TravelRequest $travelRequest): bool
    {
        return $this->travelRequestService->canUpdate($user, $travelRequest);
    }

    public function delete(User $user, TravelRequest $travelRequest): bool
    {
        return $this->travelRequestService->canDelete($user, $travelRequest);
    }

    public function updateStatus(User $user, TravelRequest $travelRequest): bool
    {
        return $this->travelRequestService->canUpdateStatus($user, $travelRequest);
    }
}
