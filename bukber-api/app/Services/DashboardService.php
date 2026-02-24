<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\PesertaHari;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function (): array {
            $totalPeserta = Peserta::query()->count();
            $avgBudget = (int) (Peserta::query()->avg('budget_per_orang') ?? 0);
            $minBudget = (int) (Peserta::query()->min('budget_per_orang') ?? 0);
            $maxBudget = (int) (Peserta::query()->max('budget_per_orang') ?? 0);

            $distribusiRaw = Peserta::query()
                ->select('minggu', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('minggu')
                ->pluck('jumlah', 'minggu');

            $distribusiMinggu = collect([1, 2, 3, 4])->map(static function (int $minggu) use ($distribusiRaw): array {
                return [
                    'minggu' => $minggu,
                    'jumlah' => (int) ($distribusiRaw[$minggu] ?? 0),
                ];
            })->values();

            $mingguFavorit = $distribusiMinggu
                ->sortByDesc('jumlah')
                ->first(static fn (array $item): bool => $item['jumlah'] > 0);

            [$rekomendasiHari, $transparansiHari] = $this->buildHariRecommendation($totalPeserta, $avgBudget);

            return [
                'total_peserta' => $totalPeserta,
                'rata_rata_budget' => $avgBudget,
                'min_budget' => $minBudget,
                'max_budget' => $maxBudget,
                'minggu_terfavorit' => $mingguFavorit ? [
                    'minggu' => $mingguFavorit['minggu'],
                    'jumlah_peserta' => $mingguFavorit['jumlah'],
                ] : null,
                'distribusi_minggu' => $distribusiMinggu->all(),
                'rekomendasi_hari' => $rekomendasiHari,
                'transparansi_hari' => $transparansiHari,
            ];
        });
    }

    public function getChartHari(): array
    {
        return Cache::remember('chart_hari', 60, function (): array {
            $raw = PesertaHari::query()
                ->select('hari', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('hari')
                ->pluck('jumlah', 'hari');

            return collect(PesertaHari::HARI_LIST)
                ->map(static function (string $hari) use ($raw): array {
                    return [
                        'hari' => $hari,
                        'jumlah' => (int) ($raw[$hari] ?? 0),
                    ];
                })
                ->values()
                ->all();
        });
    }

    public function getChartMinggu(): array
    {
        return Cache::remember('chart_minggu', 60, function (): array {
            return collect($this->getStats()['distribusi_minggu'])
                ->map(static fn (array $item): array => [
                    'label' => "Minggu {$item['minggu']}",
                    'minggu' => $item['minggu'],
                    'jumlah' => $item['jumlah'],
                ])
                ->values()
                ->all();
        });
    }

    public function getChartBudget(): array
    {
        return Cache::remember('chart_budget', 60, function (): array {
            $ranges = [
                '< 50rb' => [0, 49999],
                '50rb - 100rb' => [50000, 100000],
                '100rb - 150rb' => [100001, 150000],
                '150rb - 250rb' => [150001, 250000],
                '> 250rb' => [250001, null],
            ];

            $result = [];

            foreach ($ranges as $label => [$start, $end]) {
                $query = Peserta::query();

                if ($end === null) {
                    $query->where('budget_per_orang', '>=', $start);
                } else {
                    $query->whereBetween('budget_per_orang', [$start, $end]);
                }

                $result[] = [
                    'label' => $label,
                    'jumlah' => $query->count(),
                ];
            }

            return ['ranges' => $result];
        });
    }

    public function getResponden(int $perPage = 20, ?string $availability = null): LengthAwarePaginator
    {
        $query = Peserta::query()
            ->select([
                'id',
                'uuid',
                'nama_lengkap',
                'minggu',
                'budget_per_orang',
                'catatan',
                'created_at',
                'updated_at',
            ])
            ->with([
                'hari:id,peserta_id,hari',
                'lokasi:id,peserta_id,nama_tempat,alamat,latitude,longitude,google_place_id,created_at',
            ])
            ->latest();

        if ($availability === 'bisa') {
            $query->has('hari', '>=', 3);
        } elseif ($availability === 'mungkin') {
            $query->has('hari', '=', 2);
        } elseif ($availability === 'tidak') {
            $query->has('hari', '<=', 1);
        }

        return $query
            ->paginate($perPage)
            ->withQueryString();
    }

    private function buildHariRecommendation(int $totalPeserta, int $fallbackBudget): array
    {
        $aggregateByHari = PesertaHari::query()
            ->join('peserta', 'peserta.id', '=', 'peserta_hari.peserta_id')
            ->select(
                'peserta_hari.hari',
                DB::raw('COUNT(*) as jumlah_peserta'),
                DB::raw('AVG(peserta.budget_per_orang) as rata_rata_budget')
            )
            ->groupBy('peserta_hari.hari')
            ->get()
            ->keyBy('hari');

        $transparansiHari = collect(PesertaHari::HARI_LIST)
            ->map(static function (string $hari) use ($aggregateByHari, $totalPeserta): array {
                $aggregate = $aggregateByHari->get($hari);
                $jumlahPeserta = (int) ($aggregate->jumlah_peserta ?? 0);
                $persentasePeserta = $totalPeserta > 0
                    ? round(($jumlahPeserta / $totalPeserta) * 100, 2)
                    : 0.0;
                $rataRataBudget = $jumlahPeserta > 0
                    ? (int) round((float) ($aggregate->rata_rata_budget ?? 0))
                    : null;

                return [
                    'hari' => $hari,
                    'jumlah_peserta' => $jumlahPeserta,
                    'persentase_peserta' => $persentasePeserta,
                    'rata_rata_budget' => $rataRataBudget,
                ];
            })
            ->values();

        $hariOrder = array_flip(PesertaHari::HARI_LIST);
        $rankingHari = $transparansiHari
            ->sort(static function (array $a, array $b) use ($hariOrder): int {
                if ($a['jumlah_peserta'] !== $b['jumlah_peserta']) {
                    return $b['jumlah_peserta'] <=> $a['jumlah_peserta'];
                }

                $budgetA = $a['rata_rata_budget'] ?? PHP_INT_MAX;
                $budgetB = $b['rata_rata_budget'] ?? PHP_INT_MAX;

                if ($budgetA !== $budgetB) {
                    return $budgetA <=> $budgetB;
                }

                return ($hariOrder[$a['hari']] ?? 999) <=> ($hariOrder[$b['hari']] ?? 999);
            })
            ->values();

        $kandidatUtama = $rankingHari->first();
        if (! $kandidatUtama || $kandidatUtama['jumlah_peserta'] === 0) {
            return [null, $transparansiHari->all()];
        }

        $tieGroup = $rankingHari
            ->filter(static fn (array $item): bool => $item['jumlah_peserta'] === $kandidatUtama['jumlah_peserta'])
            ->values();
        $isTie = $tieGroup->count() > 1;

        $rekomendasiHari = [
            'hari' => $kandidatUtama['hari'],
            'jumlah_peserta' => $kandidatUtama['jumlah_peserta'],
            'persentase_peserta' => $kandidatUtama['persentase_peserta'],
            'rata_rata_budget' => $kandidatUtama['rata_rata_budget'] ?? $fallbackBudget,
            'is_tie' => $isTie,
            'tie_breaker' => $isTie ? 'budget_terendah' : 'jumlah_peserta_tertinggi',
            'kandidat_teratas' => $rankingHari->take(3)->values()->all(),
        ];

        return [$rekomendasiHari, $transparansiHari->all()];
    }
}
