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
                ['hari' => 'jumat'],
                ['hari' => 'sabtu'],
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
                ],
            ]);
    }

    public function test_dashboard_chart_hari_returns_all_days(): void
    {
        $peserta = Peserta::factory()->create();
        collect(PesertaHari::HARI_LIST)->each(
            fn (string $hari) => $peserta->hari()->create(['hari' => $hari])
        );

        $response = $this->getJson('/api/v1/dashboard/chart/hari');

        $response->assertOk()
            ->assertJsonCount(7, 'data')
            ->assertJsonPath('data.0.hari', 'senin');
    }

    public function test_dashboard_stats_returns_dynamic_recommendation_with_transparency(): void
    {
        $jumatA = Peserta::factory()->create(['budget_per_orang' => 150000]);
        $jumatA->hari()->createMany([['hari' => 'jumat']]);

        $jumatB = Peserta::factory()->create(['budget_per_orang' => 170000]);
        $jumatB->hari()->createMany([['hari' => 'jumat']]);

        $sabtu = Peserta::factory()->create(['budget_per_orang' => 140000]);
        $sabtu->hari()->createMany([['hari' => 'sabtu']]);

        $response = $this->getJson('/api/v1/dashboard/stats');

        $response->assertOk()
            ->assertJsonPath('data.rekomendasi_hari.hari', 'jumat')
            ->assertJsonPath('data.rekomendasi_hari.jumlah_peserta', 2)
            ->assertJsonCount(7, 'data.transparansi_hari');
    }

    public function test_dashboard_responden_can_be_filtered_by_availability(): void
    {
        $pesertaBisa = Peserta::factory()->create(['nama_lengkap' => 'Peserta Bisa']);
        $pesertaBisa->hari()->createMany([
            ['hari' => 'senin'],
            ['hari' => 'selasa'],
            ['hari' => 'rabu'],
        ]);

        $pesertaMungkin = Peserta::factory()->create(['nama_lengkap' => 'Peserta Mungkin']);
        $pesertaMungkin->hari()->createMany([
            ['hari' => 'kamis'],
            ['hari' => 'jumat'],
        ]);

        $pesertaTidak = Peserta::factory()->create(['nama_lengkap' => 'Peserta Tidak']);
        $pesertaTidak->hari()->createMany([
            ['hari' => 'sabtu'],
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

    public function test_dashboard_responden_rejects_invalid_availability_filter(): void
    {
        $response = $this->getJson('/api/v1/dashboard/responden?availability=semua');

        $response->assertStatus(422)
            ->assertJsonPath('errors.availability.0', 'The selected availability is invalid.');
    }
}
