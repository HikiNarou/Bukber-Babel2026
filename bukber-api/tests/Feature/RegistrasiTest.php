<?php

namespace Tests\Feature;

use App\Models\Peserta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_submit_registrasi(): void
    {
        $response = $this->withHeaders([
            'X-Device-Fingerprint' => 'test-device-123',
        ])->postJson('/api/v1/registrasi', [
            'nama_lengkap' => 'Ahmad Fauzi',
            'preferensi_minggu' => [
                [
                    'minggu' => 1,
                    'hari' => ['senin', 'selasa'],
                ],
                [
                    'minggu' => 4,
                    'hari' => ['senin', 'sabtu'],
                ],
            ],
            'budget_per_orang' => 150000,
            'catatan' => 'No spicy',
            'lokasi' => [
                'nama_tempat' => 'Warung Sate Pak Haji',
                'alamat' => 'Jl. Merdeka No. 10',
                'latitude' => -6.2088,
                'longitude' => 106.8456,
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.nama_lengkap', 'Ahmad Fauzi')
            ->assertJsonPath('data.minggu.0', 1)
            ->assertJsonPath('data.minggu.1', 4)
            ->assertJsonPath('data.preferensi_minggu.0.minggu', 1)
            ->assertJsonPath('data.preferensi_minggu.1.minggu', 4);

        $this->assertDatabaseHas('peserta', [
            'nama_lengkap' => 'Ahmad Fauzi',
            'minggu' => 1,
        ]);
        $this->assertDatabaseHas('peserta_hari', ['minggu' => 1, 'hari' => 'senin']);
        $this->assertDatabaseHas('peserta_hari', ['minggu' => 4, 'hari' => 'sabtu']);
        $this->assertDatabaseHas('lokasi', ['nama_tempat' => 'Warung Sate Pak Haji']);
    }

    public function test_registrasi_validation_fails_without_nama(): void
    {
        $response = $this->postJson('/api/v1/registrasi', [
            'preferensi_minggu' => [
                [
                    'minggu' => 2,
                    'hari' => ['senin'],
                ],
            ],
            'budget_per_orang' => 100000,
            'lokasi' => [
                'nama_tempat' => 'Test',
            ],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nama_lengkap']);
    }

    public function test_can_update_registrasi_before_deadline(): void
    {
        $store = $this->withHeaders([
            'X-Device-Fingerprint' => 'device-for-update',
        ])->postJson('/api/v1/registrasi', [
            'nama_lengkap' => 'Budi Santoso',
            'preferensi_minggu' => [
                [
                    'minggu' => 1,
                    'hari' => ['selasa'],
                ],
            ],
            'budget_per_orang' => 100000,
            'lokasi' => [
                'nama_tempat' => 'Cafe Lama',
            ],
        ]);

        $uuid = $store->json('data.uuid');
        $this->assertNotEmpty($uuid);

        $update = $this->withHeaders([
            'X-Device-Fingerprint' => 'device-for-update',
        ])->putJson("/api/v1/registrasi/{$uuid}", [
            'nama_lengkap' => 'Budi Santoso',
            'preferensi_minggu' => [
                [
                    'minggu' => 3,
                    'hari' => ['sabtu', 'minggu'],
                ],
                [
                    'minggu' => 4,
                    'hari' => ['senin'],
                ],
            ],
            'budget_per_orang' => 200000,
            'lokasi' => [
                'nama_tempat' => 'Resto Baru',
                'alamat' => 'Jakarta Selatan',
            ],
        ]);

        $update->assertOk()
            ->assertJsonPath('data.minggu.0', 3)
            ->assertJsonPath('data.minggu.1', 4)
            ->assertJsonPath('data.budget_per_orang', 200000)
            ->assertJsonPath('data.lokasi.nama_tempat', 'Resto Baru')
            ->assertJsonPath('data.preferensi_minggu.0.minggu', 3)
            ->assertJsonPath('data.preferensi_minggu.1.minggu', 4);

        $this->assertEquals(1, Peserta::query()->count());
    }

    public function test_can_submit_registrasi_without_optional_address_and_coordinates(): void
    {
        $response = $this->withHeaders([
            'X-Device-Fingerprint' => 'test-device-optional-location',
        ])->postJson('/api/v1/registrasi', [
            'nama_lengkap' => 'Fajar Ramadhan',
            'preferensi_minggu' => [
                [
                    'minggu' => 2,
                    'hari' => ['jumat'],
                ],
            ],
            'budget_per_orang' => 150000,
            'catatan' => '',
            'lokasi' => [
                'nama_tempat' => 'Resto Tanpa Koordinat',
                'alamat' => '',
                'latitude' => '',
                'longitude' => '',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.lokasi.nama_tempat', 'Resto Tanpa Koordinat')
            ->assertJsonPath('data.lokasi.alamat', null)
            ->assertJsonPath('data.lokasi.latitude', null)
            ->assertJsonPath('data.lokasi.longitude', null);

        $this->assertDatabaseHas('lokasi', [
            'nama_tempat' => 'Resto Tanpa Koordinat',
            'alamat' => null,
            'latitude' => null,
            'longitude' => null,
        ]);
    }
}
