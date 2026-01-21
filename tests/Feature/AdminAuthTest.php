<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_when_accessing_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('admin.login'));
    }

    public function test_web_authenticated_user_is_redirected_to_peserta_index_when_accessing_admin_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($user, 'web')->get('/admin');

        $response->assertRedirect(route('peserta.index'));
    }

    public function test_admin_can_login_and_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create([
            'status_akun' => 'aktif',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs($admin, 'admin');

        $this->get('/admin')->assertOk();
    }

    public function test_non_admin_user_cannot_login_via_admin_form(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status_akun' => 'aktif',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertGuest('admin');
    }

    public function test_admin_middleware_rejects_non_admin_authenticated_on_admin_guard(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($user, 'admin')->get('/admin');

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest('admin');
    }

    public function test_admin_user_seeder_creates_admin_account(): void
    {
        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@gmail.com',
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);
    }
}
