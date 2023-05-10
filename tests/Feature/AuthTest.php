<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->createUser();
    }

    public function test_login_redirect_to_homepage(): void
    {
        $user = $this->user;
        $response = $this->post('login', [
            'email' => $user->email,
            'password' => $user->password,
        ]);
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_unauthenticated_user_cannot_see_auhorized_action(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    private function createUser(): User
    {
        return User::factory()->create();
    }
}
