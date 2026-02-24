<?php

namespace App\Services;

use App\Models\Peserta;
use App\Models\PesertaHari;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardService
{
    private const MINGGU_LIST = [1, 2, 3, 4];
    private ?bool $hasPesertaHariMingguColumn = null;

    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 60, function (): array {
            $totalPeserta = Peserta::query()->count();
            $avgBudget = (int) (Peserta::query()->avg('budget_per_orang') ?? 0);
            $minBudget = (int) (Peserta::query()->min('budget_per_orang') ?? 0);
            $maxBudget = (int) (Peserta::query()->max('budget_per_orang') ?? 0);

            if ($this->hasMingguOnPesertaHari()) {
                $distribusiRows = PesertaHari::query()
                    ->selectRaw('peserta_hari.minggu as minggu')
                    ->selectRaw('COUNT(DISTINCT peserta_hari.peserta_id) as jumlah')
                    ->groupBy('peserta_hari.minggu')
                    ->get();
            } else {
                $distribusiRows = Peserta::query()
                    ->selectRaw('peserta.minggu as minggu')
                    ->selectRaw('COUNT(*) as jumlah')
                    ->groupBy('peserta.minggu')
                    ->get();
            }

            $distribusiRaw = $distribusiRows->mapWithKeys(static function ($row): array {
                $minggu = (int) data_get($row, 'minggu', 0);
                $jumlah = (int) data_get($row, 'jumlah', 0);

                if ($minggu < 1 || $minggu > 4) {
                    return [];
                }

                return [$minggu => $jumlah];
            });

            $distribusiMinggu = collect(self::MINGGU_LIST)->map(static function (int $minggu) use ($distribusiRaw): array {
                return [
                    'minggu' => $minggu,
                    'jumlah' => (int) ($distribusiRaw[$minggu] ?? 0),
                ];
            })->values();

            $mingguFavorit = $distribusiMinggu
                ->sort(static fn (array $a, array $b): int => ($b['jumlah'] <=> $a['jumlah']) ?: ($a['minggu'] <=> $b['minggu']))
                ->first(static fn (array $item): bool => $item['jumlah'] > 0);

            [$rekomendasiHari, $transparansiHari, $detailKetersediaan] = $this->buildHariRecommendation($totalPeserta);

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
                'detail_ketersediaan' => $detailKetersediaan,
            ];
        });
    }

    public function getChartHari(): array
    {
        return Cache::remember('chart_hari', 60, function (): array {
            $rows = PesertaHari::query()
                ->selectRaw('peserta_hari.hari as hari')
                ->selectRaw('COUNT(DISTINCT peserta_hari.peserta_id) as jumlah')
                ->groupBy('peserta_hari.hari')
                ->get();

            $raw = $rows->mapWithKeys(static function ($row): array {
                $hari = (string) data_get($row, 'hari', '');
                $jumlah = (int) data_get($row, 'jumlah', 0);

                if ($hari === '') {
                    return [];
                }

                return [$hari => $jumlah];
            });

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
        $hariSelect = $this->hasMingguOnPesertaHari()
            ? 'id,peserta_id,minggu,hari'
            : 'id,peserta_id,hari';

        $query = Peserta::query()
            ->select([
                'id',
                'uuid',
                'nama_lengkap',
                'budget_per_orang',
                'catatan',
                'created_at',
                'updated_at',
            ])
            ->withCount('hari')
            ->with([
                "hari:{$hariSelect}",
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

    private function buildHariRecommendation(int $totalPeserta): array
    {
        if ($this->hasMingguOnPesertaHari()) {
            $aggregateRows = PesertaHari::query()
                ->selectRaw('peserta_hari.minggu as minggu')
                ->selectRaw('peserta_hari.hari as hari')
                ->selectRaw('COUNT(DISTINCT peserta_hari.peserta_id) as jumlah_peserta')
                ->groupBy('peserta_hari.minggu', 'peserta_hari.hari')
                ->get();
        } else {
            $aggregateRows = PesertaHari::query()
                ->join('peserta', 'peserta.id', '=', 'peserta_hari.peserta_id')
                ->selectRaw('peserta.minggu as minggu')
                ->selectRaw('peserta_hari.hari as hari')
                ->selectRaw('COUNT(DISTINCT peserta_hari.peserta_id) as jumlah_peserta')
                ->groupBy('peserta.minggu', 'peserta_hari.hari')
                ->get();
        }

        $aggregateByHari = $aggregateRows
            ->keyBy(static fn ($item): string => sprintf(
                '%d:%s',
                (int) data_get($item, 'minggu', 0),
                (string) data_get($item, 'hari', '')
            ));

        $transparansiHari = collect(self::MINGGU_LIST)
            ->flatMap(function (int $minggu) use ($aggregateByHari, $totalPeserta) {
                return collect(PesertaHari::HARI_LIST)->map(function (string $hari) use ($aggregateByHari, $minggu, $totalPeserta): array {
                    $aggregate = $aggregateByHari->get($this->buildSlotKey($minggu, $hari));
                    $jumlahPeserta = (int) ($aggregate->jumlah_peserta ?? 0);
                    $persentasePeserta = $totalPeserta > 0
                        ? round(($jumlahPeserta / $totalPeserta) * 100, 2)
                        : 0.0;

                    return [
                        'minggu' => $minggu,
                        'hari' => $hari,
                        'jumlah_peserta' => $jumlahPeserta,
                        'persentase_peserta' => $persentasePeserta,
                    ];
                });
            })
            ->values();

        $hariOrder = array_flip(PesertaHari::HARI_LIST);
        $rankingHari = $transparansiHari
            ->sort(static function (array $a, array $b) use ($hariOrder): int {
                if ($a['jumlah_peserta'] !== $b['jumlah_peserta']) {
                    return $b['jumlah_peserta'] <=> $a['jumlah_peserta'];
                }

                if ($a['minggu'] !== $b['minggu']) {
                    return $a['minggu'] <=> $b['minggu'];
                }

                return ($hariOrder[$a['hari']] ?? 999) <=> ($hariOrder[$b['hari']] ?? 999);
            })
            ->values();

        $kandidatUtama = $rankingHari->first();
        if (! $kandidatUtama || $kandidatUtama['jumlah_peserta'] === 0) {
            return [null, $transparansiHari->all(), $this->buildDetailKetersediaan($transparansiHari)];
        }

        $tieGroup = $rankingHari
            ->filter(static fn (array $item): bool => $item['jumlah_peserta'] === $kandidatUtama['jumlah_peserta'])
            ->values();
        $isTie = $tieGroup->count() > 1;

        $rekomendasiHari = [
            'minggu' => $kandidatUtama['minggu'],
            'hari' => $kandidatUtama['hari'],
            'jumlah_peserta' => $kandidatUtama['jumlah_peserta'],
            'persentase_peserta' => $kandidatUtama['persentase_peserta'],
            'is_tie' => $isTie,
            'tie_breaker' => $isTie ? 'minggu_terawal_lalu_hari_terawal' : 'jumlah_peserta_tertinggi',
            'kandidat_teratas' => $rankingHari->take(5)->values()->all(),
        ];

        return [$rekomendasiHari, $transparansiHari->all(), $this->buildDetailKetersediaan($transparansiHari)];
    }

    private function buildDetailKetersediaan(\Illuminate\Support\Collection $transparansiHari): array
    {
        $transparansiMap = $transparansiHari
            ->keyBy(fn (array $item): string => $this->buildSlotKey((int) $item['minggu'], (string) $item['hari']));

        return collect(self::MINGGU_LIST)
            ->map(function (int $minggu) use ($transparansiMap): array {
                return [
                    'minggu' => $minggu,
                    'hari' => collect(PesertaHari::HARI_LIST)
                        ->map(function (string $hari) use ($minggu, $transparansiMap): array {
                            $item = $transparansiMap->get($this->buildSlotKey($minggu, $hari));

                            return [
                                'hari' => $hari,
                                'jumlah_peserta' => (int) ($item['jumlah_peserta'] ?? 0),
                                'persentase_peserta' => (float) ($item['persentase_peserta'] ?? 0),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    private function buildSlotKey(int $minggu, string $hari): string
    {
        return "{$minggu}:{$hari}";
    }

    private function hasMingguOnPesertaHari(): bool
    {
        if ($this->hasPesertaHariMingguColumn === null) {
            $this->hasPesertaHariMingguColumn = Schema::hasColumn('peserta_hari', 'minggu');
        }

        return $this->hasPesertaHariMingguColumn;
    }
}
