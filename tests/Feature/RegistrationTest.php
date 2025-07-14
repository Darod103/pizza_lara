<?php

namespace Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;


    public function test_registration_success(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@test.ru'
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'name',
            'email',
            'access_token',
            'expires_in'
        ]);
    }

    public function test_registration_fail(): void
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);
        $response = $this->postJson('api/register', [
            'name' => 'Test User',
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);
        $response->assertStatus(422);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_fail_with_wrong_password(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'Test User',
            'email' => 'test@ro',
            'password' => '',
        ]);

        $response->assertStatus(422);
        $response->assertInvalid('password');
    }

    public function test_registration_fail_without_email(): void
    {
        $response = $this->postJson('api/register', [
            'name' => 'Test User',
            'password' => '',
        ]);
        $response->assertStatus(422);
        $response->assertInvalid('email');
    }

    public function test_registration_fail_without_name(): void
    {
        $response = $this->postJson('api/register', [
            'email' => 'test@test.ru',
            'password' => '123456',
        ]);
        $response->assertStatus(422);
    }

}
