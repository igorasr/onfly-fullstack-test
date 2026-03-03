<?php

namespace Tests\Feature;

use App\Enums\TravelRequestStatus;
use App\Models\TravelRequest;
use App\Models\User;
use App\Notifications\TravelRequestStatusUpdatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelRequestApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_travel_request(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/travel-requests', [
            'destination' => 'Recife',
            'departure_date' => '2026-04-10',
            'return_date' => '2026-04-15',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.requester_name', $user->name)
            ->assertJsonPath('data.status', TravelRequestStatus::Requested->value);

        $this->assertDatabaseHas('travel_requests', [
            'requester_id' => $user->id,
            'status' => TravelRequestStatus::Requested->value,
        ]);
    }

    public function test_user_can_consult_own_travel_request(): void
    {
        $user = User::factory()->create();

        $travelRequest = TravelRequest::factory()->create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson("/api/travel-requests/{$travelRequest->id}");

        $response
            ->assertOk()
            ->assertJsonPath('id', $travelRequest->id);
    }

    public function test_user_can_filter_travel_requests_by_status_destination_and_period(): void
    {
        $user = User::factory()->create();

        TravelRequest::factory()->create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'destination' => 'São Paulo',
            'status' => TravelRequestStatus::Requested,
            'departure_date' => '2026-05-10',
            'return_date' => '2026-05-14',
        ]);

        TravelRequest::factory()->create([
            'requester_id' => $user->id,
            'requester_name' => $user->name,
            'destination' => 'Rio de Janeiro',
            'status' => TravelRequestStatus::Approved,
            'departure_date' => '2026-06-01',
            'return_date' => '2026-06-04',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/travel-requests?status=requested&destination=Paulo&travel_from=2026-05-01&travel_to=2026-05-30');

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.destination', 'São Paulo')
            ->assertJsonPath('data.0.status', TravelRequestStatus::Requested->value);
    }

    public function test_non_admin_cannot_update_status(): void
    {
        $requester = User::factory()->create();
        $nonAdmin = User::factory()->create();

        $travelRequest = TravelRequest::factory()->create([
            'requester_id' => $requester->id,
            'requester_name' => $requester->name,
            'status' => TravelRequestStatus::Requested,
        ]);

        Sanctum::actingAs($nonAdmin);

        $response = $this->patchJson("/api/travel-requests/{$travelRequest->id}/status", [
            'status' => TravelRequestStatus::Approved->value,
        ]);

        $response->assertForbidden();
    }

    public function test_admin_can_approve_and_notifies_requester(): void
    {
        Notification::fake();

        $requester = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $travelRequest = TravelRequest::factory()->create([
            'requester_id' => $requester->id,
            'requester_name' => $requester->name,
            'status' => TravelRequestStatus::Requested,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/travel-requests/{$travelRequest->id}/status", [
            'status' => TravelRequestStatus::Approved->value,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('status', TravelRequestStatus::Approved->value);

        Notification::assertSentTo($requester, TravelRequestStatusUpdatedNotification::class);
    }

    public function test_admin_cannot_change_status_of_own_request(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $travelRequest = TravelRequest::factory()->create([
            'requester_id' => $admin->id,
            'requester_name' => $admin->name,
            'status' => TravelRequestStatus::Requested,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/travel-requests/{$travelRequest->id}/status", [
            'status' => TravelRequestStatus::Approved->value,
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_cancel_after_approval(): void
    {
        $requester = User::factory()->create();
        $admin = User::factory()->create(['is_admin' => true]);

        $travelRequest = TravelRequest::factory()->create([
            'requester_id' => $requester->id,
            'requester_name' => $requester->name,
            'status' => TravelRequestStatus::Approved,
        ]);

        Sanctum::actingAs($admin);

        $response = $this->patchJson("/api/travel-requests/{$travelRequest->id}/status", [
            'status' => TravelRequestStatus::Cancelled->value,
        ]);

        $response->assertStatus(422);
    }
}
