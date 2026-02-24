<?php

namespace Tests\Unit;

use App\Models\Peserta;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_stats_returns_expected_keys(): void
    {
        Cache::forget('dashboard_stats');

        Peserta::factory()->count(10)->create();

        $stats = app(DashboardService::class)->getStats();

        $this->assertArrayHasKey('total_peserta', $stats);
        $this->assertArrayHasKey('rata_rata_budget', $stats);
        $this->assertArrayHasKey('minggu_terfavorit', $stats);
        $this->assertArrayHasKey('rekomendasi_hari', $stats);
        $this->assertArrayHasKey('transparansi_hari', $stats);
        $this->assertEquals(10, $stats['total_peserta']);
    }

    public function test_get_stats_recommendation_prioritizes_availability_then_lower_budget_when_tie(): void
    {
        Cache::forget('dashboard_stats');

        $seninA = Peserta::factory()->create(['budget_per_orang' => 200000]);
        $seninA->hari()->createMany([['hari' => 'senin']]);

        $seninB = Peserta::factory()->create(['budget_per_orang' => 180000]);
        $seninB->hari()->createMany([['hari' => 'senin']]);

        $selasaA = Peserta::factory()->create(['budget_per_orang' => 120000]);
        $selasaA->hari()->createMany([['hari' => 'selasa']]);

        $selasaB = Peserta::factory()->create(['budget_per_orang' => 110000]);
        $selasaB->hari()->createMany([['hari' => 'selasa']]);

        $rabu = Peserta::factory()->create(['budget_per_orang' => 150000]);
        $rabu->hari()->createMany([['hari' => 'rabu']]);

        $stats = app(DashboardService::class)->getStats();

        $this->assertNotNull($stats['rekomendasi_hari']);
        $this->assertSame('selasa', $stats['rekomendasi_hari']['hari']);
        $this->assertTrue($stats['rekomendasi_hari']['is_tie']);
        $this->assertSame('budget_terendah', $stats['rekomendasi_hari']['tie_breaker']);
    }

    public function test_get_responden_eager_loads_required_relations_to_prevent_n_plus_one(): void
    {
        $peserta = Peserta::factory()->create();
        $peserta->hari()->createMany([
            ['hari' => 'jumat'],
            ['hari' => 'sabtu'],
        ]);
        $peserta->lokasi()->create([
            'nama_tempat' => 'Resto A',
            'alamat' => 'Jakarta Selatan',
        ]);

        $paginator = app(DashboardService::class)->getResponden(10);
        $first = $paginator->getCollection()->first();

        $this->assertNotNull($first);
        $this->assertTrue($first->relationLoaded('hari'));
        $this->assertTrue($first->relationLoaded('lokasi'));
    }
}
