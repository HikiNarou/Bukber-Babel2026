<?php

namespace Tests\Feature;

use App\Models\Peserta;
use App\Models\PesertaHari;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_dashboard_stats_endpoint_returns_expected_structure(): void
    {
        $peserta = Peserta::factory()->count(6)->create();

        foreach ($peserta as $item) {
            $item->hari()->createMany([
                ['minggu' => 2, 'hari' => 'jumat'],
                ['minggu' => 2, 'hari' => 'sabtu'],
            ]);
            $item->lokasi()->create([
                'nama_tempat' => 'Lokasi '.$item->id,
                'alamat' => 'Jakarta',
            ]);
        }

        $response = $this->getJson('/api/v1/dashboard/stats');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'total_peserta',
                    'rata_rata_budget',
                    'min_budget',
                    'max_budget',
                    'minggu_terfavorit',
                    'distribusi_minggu',
                    'rekomendasi_hari',
                    'transparansi_hari',
                    'detail_ketersediaan',
                ],
            ]);
    }

    public function test_dashboard_chart_hari_returns_all_days(): void
    {
        $peserta = Peserta::factory()->create();
        collect(PesertaHari::HARI_LIST)->each(
            fn (string $hari) => $peserta->hari()->create(['minggu' => 2, 'hari' => $hari])
        );

        $response = $this->getJson('/api/v1/dashboard/chart/hari');

        $response->assertOk()
            ->assertJsonCount(7, 'data')
            ->assertJsonPath('data.0.hari', 'senin');
    }

    public function test_dashboard_stats_returns_dynamic_recommendation_with_transparency(): void
    {
        $jumatA = Peserta::factory()->create(['budget_per_orang' => 150000]);
        $jumatA->hari()->createMany([['minggu' => 1, 'hari' => 'jumat']]);

        $jumatB = Peserta::factory()->create(['budget_per_orang' => 170000]);
        $jumatB->hari()->createMany([['minggu' => 1, 'hari' => 'jumat']]);

        $sabtu = Peserta::factory()->create(['budget_per_orang' => 140000]);
        $sabtu->hari()->createMany([
            ['minggu' => 2, 'hari' => 'sabtu'],
            ['minggu' => 1, 'hari' => 'jumat'],
        ]);

        $response = $this->getJson('/api/v1/dashboard/stats');

        $response->assertOk()
            ->assertJsonPath('data.rekomendasi_hari.minggu', 1)
            ->assertJsonPath('data.rekomendasi_hari.hari', 'jumat')
            ->assertJsonPath('data.rekomendasi_hari.jumlah_peserta', 3)
            ->assertJsonCount(28, 'data.transparansi_hari')
            ->assertJsonCount(4, 'data.detail_ketersediaan');
    }

    public function test_dashboard_responden_can_be_filtered_by_availability(): void
    {
        $pesertaBisa = Peserta::factory()->create(['nama_lengkap' => 'Peserta Bisa']);
        $pesertaBisa->hari()->createMany([
            ['minggu' => 1, 'hari' => 'senin'],
            ['minggu' => 1, 'hari' => 'selasa'],
            ['minggu' => 2, 'hari' => 'rabu'],
        ]);

        $pesertaMungkin = Peserta::factory()->create(['nama_lengkap' => 'Peserta Mungkin']);
        $pesertaMungkin->hari()->createMany([
            ['minggu' => 3, 'hari' => 'kamis'],
            ['minggu' => 3, 'hari' => 'jumat'],
        ]);

        $pesertaTidak = Peserta::factory()->create(['nama_lengkap' => 'Peserta Tidak']);
        $pesertaTidak->hari()->createMany([
            ['minggu' => 4, 'hari' => 'sabtu'],
        ]);

        $bisaResponse = $this->getJson('/api/v1/dashboard/responden?availability=bisa');
        $bisaResponse->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.nama_lengkap', 'Peserta Bisa');

        $mungkinResponse = $this->getJson('/api/v1/dashboard/responden?availability=mungkin');
        $mungkinResponse->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.nama_lengkap', 'Peserta Mungkin');

        $tidakResponse = $this->getJson('/api/v1/dashboard/responden?availability=tidak');
        $tidakResponse->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.nama_lengkap', 'Peserta Tidak');
    }

    public function test_dashboard_responden_supports_pagination(): void
    {
        $peserta = Peserta::factory()->count(12)->create();

        foreach ($peserta as $item) {
            $item->hari()->create([
                'minggu' => 1,
                'hari' => 'senin',
            ]);
        }

        $response = $this->getJson('/api/v1/dashboard/responden?per_page=5&page=2');

        $response->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 12)
            ->assertJsonPath('meta.page', 2)
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.last_page', 3);
    }

    public function test_dashboard_responden_rejects_invalid_availability_filter(): void
    {
        $response = $this->getJson('/api/v1/dashboard/responden?availability=semua');

        $response->assertStatus(422)
            ->assertJsonPath('errors.availability.0', 'The selected availability is invalid.');
    }
}
