<?php

namespace Tests\Unit;

use App\Enums\TravelRequestStatus;
use App\Models\TravelRequest;
use App\Models\User;
use App\Services\TravelRequestService;
use PHPUnit\Framework\TestCase;

class TravelRequestServiceTest extends TestCase
{
    public function test_admin_can_update_status_when_not_requester(): void
    {
        $service = new TravelRequestService;

        $admin = new User;
        $admin->id = 10;
        $admin->is_admin = true;

        $travelRequest = new TravelRequest;
        $travelRequest->requester_id = 1;

        $this->assertTrue($service->canUpdateStatus($admin, $travelRequest));
    }

    public function test_non_admin_cannot_update_status_even_if_not_requester(): void
    {
        $service = new TravelRequestService;

        $user = new User;
        $user->id = 2;
        $user->is_admin = false;

        $travelRequest = new TravelRequest;
        $travelRequest->requester_id = 1;

        $this->assertTrue($service->canUpdateStatus($user, $travelRequest));
    }

    public function test_admin_can_update_status_of_own_request(): void
    {
        $service = new TravelRequestService;

        $admin = new User;
        $admin->id = 7;
        $admin->is_admin = true;

        $travelRequest = new TravelRequest;
        $travelRequest->requester_id = 7;

        $this->assertTrue($service->canUpdateStatus($admin, $travelRequest));
    }

    public function test_status_transition_rules_are_enforced(): void
    {
        $service = new TravelRequestService;

        $this->assertTrue(TravelRequestStatus::Requested->canTransitionTo(TravelRequestStatus::Approved));
        $this->assertTrue(TravelRequestStatus::Requested->canTransitionTo(TravelRequestStatus::Cancelled));

        $this->assertFalse(TravelRequestStatus::Approved->canTransitionTo(TravelRequestStatus::Cancelled));
        $this->assertFalse(TravelRequestStatus::Cancelled->canTransitionTo(TravelRequestStatus::Approved));
        $this->assertFalse(TravelRequestStatus::Requested->canTransitionTo(TravelRequestStatus::Requested));
    }

    public function test_owner_or_admin_can_view_update_and_delete(): void
    {
        $service = new TravelRequestService;

        $owner = new User;
        $owner->id = 1;
        $owner->is_admin = false;

        $admin = new User;
        $admin->id = 2;
        $admin->is_admin = true;

        $otherUser = new User;
        $otherUser->id = 3;
        $otherUser->is_admin = false;

        $travelRequest = new TravelRequest;
        $travelRequest->requester_id = 1;

        $this->assertTrue($service->canView($owner, $travelRequest));
        $this->assertTrue($service->canUpdate($owner, $travelRequest));
        $this->assertTrue($service->canDelete($owner, $travelRequest));

        $this->assertTrue($service->canView($admin, $travelRequest));
        $this->assertTrue($service->canUpdate($admin, $travelRequest));
        $this->assertTrue($service->canDelete($admin, $travelRequest));

        $this->assertFalse($service->canView($otherUser, $travelRequest));
        $this->assertFalse($service->canUpdate($otherUser, $travelRequest));
        $this->assertFalse($service->canDelete($otherUser, $travelRequest));
    }
}
