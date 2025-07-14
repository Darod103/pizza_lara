<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_auth_success(): void
    {
        $user = User::factory()->create([
                'password' => '123456',
            ]
        );
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_auth_failed(): void
    {
        $user = User::factory()->create([
            'password' => '123456',
        ]);
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => '12345',
        ]);
        $response->assertUnauthorized();
        $response->assertJsonStructure([
            'error'
        ]);

    }
}
