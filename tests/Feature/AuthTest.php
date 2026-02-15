<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['token', 'user']);
    }

    public function test_logout_revokes_token()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->assertEquals(1, $user->tokens()->count());

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/logout');

        $response->assertStatus(200);
        
        // Clear auth state to ensure next request is fresh
        $this->app['auth']->forgetGuards();

        $this->assertEquals(0, $user->tokens()->count(), 'Token count should be 0 after logout');

        // Verify token is revoked by trying to access protected route
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user')
          ->assertStatus(401);
    }
}
