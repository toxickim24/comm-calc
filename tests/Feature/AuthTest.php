<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_page_renders(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_redirected_from_login(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect();
    }

    public function test_guest_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_sales_rep_cannot_access_admin_pages(): void
    {
        $user = User::where('email', 'john@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_pages(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(200);
    }

    public function test_inactive_user_is_logged_out(): void
    {
        $user = User::where('email', 'john@baysidepavers.com')->first();
        $user->update(['is_active' => false]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_force_password_change_redirects(): void
    {
        $user = User::where('email', 'john@baysidepavers.com')->first();
        $user->update(['force_password_change' => true]);

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('password.force-change'));
    }

    public function test_logout(): void
    {
        $user = User::where('email', 'admin@baysidepavers.com')->first();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
