<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_and_receive_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response
            ->assertCreated()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure(['token', 'user' => ['id', 'name', 'email']]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me');

        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    public function test_authenticated_user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $logoutResponse = $this
            ->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/logout');

        $logoutResponse->assertNoContent();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_guest_cannot_access_me_endpoint(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    }
}
