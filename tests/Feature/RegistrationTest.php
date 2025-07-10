<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
//    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_registration_success(): void
    {

        $user = User::factory()->make();
        $response = $this->post('api/register', [
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.ru'
        ]);
        $response->assertStatus(200);
    }

    public function test_registration_fail(): void
    {
        $response = $this->post('api/register', [
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);
        $response->assertStatus(302);
    }
}
