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
            ->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'user' => ['id', 'name', 'email'],
                'authorization' => ['token', 'type'],
            ]);

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
            ->assertJsonStructure([
                'status',
                'user' => ['id', 'name', 'email'],
                'authorization' => ['token', 'type'],
            ]);
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

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_access_me_endpoint(): void
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $response = $this
            ->withToken($token)
            ->getJson('/api/me');

        $response
            ->assertOk()
            ->assertJsonPath('id', $user->id);
    }

    public function test_authenticated_user_can_logout_and_token_is_revoked(): void
    {
        $user = User::factory()->create();
        $token = auth()->login($user);

        $logoutResponse = $this
            ->withToken($token)
            ->postJson('/api/logout');

        $logoutResponse->assertOk();

        $meResponse = $this
            ->withToken($token)
            ->getJson('/api/me');

        $meResponse->assertUnauthorized();
    }
}
