<?php

namespace Tests\Feature;

use App\Models\AdminUser;
use App\Models\EventSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_and_access_protected_route(): void
    {
        AdminUser::query()->create([
            'username' => 'admin',
            'password' => Hash::make('secret123'),
        ]);

        EventSetting::query()->create([
            'nama_event' => 'Bukber',
            'is_registration_open' => true,
            'is_voting_open' => false,
        ]);

        $login = $this->postJson('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $login->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token']]);

        $token = $login->json('data.token');

        $settings = $this->withToken($token)->getJson('/api/v1/admin/settings');
        $settings->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_event', 'Bukber');
    }

    public function test_admin_login_fails_with_wrong_credentials(): void
    {
        AdminUser::query()->create([
            'username' => 'admin',
            'password' => Hash::make('secret123'),
        ]);

        $response = $this->postJson('/api/v1/admin/login', [
            'username' => 'admin',
            'password' => 'wrong123',
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('success', false);
    }
}
