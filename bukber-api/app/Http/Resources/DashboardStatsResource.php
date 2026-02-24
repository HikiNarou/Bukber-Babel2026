<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'total_peserta' => (int) ($this['total_peserta'] ?? 0),
            'rata_rata_budget' => (int) ($this['rata_rata_budget'] ?? 0),
            'min_budget' => (int) ($this['min_budget'] ?? 0),
            'max_budget' => (int) ($this['max_budget'] ?? 0),
            'minggu_terfavorit' => $this['minggu_terfavorit'] ?? null,
            'distribusi_minggu' => $this['distribusi_minggu'] ?? [],
            'rekomendasi_hari' => $this->transformRekomendasiHari(),
            'transparansi_hari' => $this->transformTransparansiHari(),
            'detail_ketersediaan' => $this->transformDetailKetersediaan(),
        ];
    }

    private function transformTransparansiHari(): array
    {
        return collect($this['transparansi_hari'] ?? [])
            ->map(static function (array $item): array {
                return [
                    'minggu' => (int) ($item['minggu'] ?? 0),
                    'hari' => (string) ($item['hari'] ?? ''),
                    'jumlah_peserta' => (int) ($item['jumlah_peserta'] ?? 0),
                    'persentase_peserta' => (float) ($item['persentase_peserta'] ?? 0),
                ];
            })
            ->values()
            ->all();
    }

    private function transformRekomendasiHari(): ?array
    {
        $rekomendasi = $this['rekomendasi_hari'] ?? null;
        if (! is_array($rekomendasi)) {
            return null;
        }

        return [
            'minggu' => (int) ($rekomendasi['minggu'] ?? 0),
            'hari' => (string) ($rekomendasi['hari'] ?? ''),
            'jumlah_peserta' => (int) ($rekomendasi['jumlah_peserta'] ?? 0),
            'persentase_peserta' => (float) ($rekomendasi['persentase_peserta'] ?? 0),
            'is_tie' => (bool) ($rekomendasi['is_tie'] ?? false),
            'tie_breaker' => (string) ($rekomendasi['tie_breaker'] ?? ''),
            'kandidat_teratas' => collect($rekomendasi['kandidat_teratas'] ?? [])
                ->map(static function (array $item): array {
                    return [
                        'minggu' => (int) ($item['minggu'] ?? 0),
                        'hari' => (string) ($item['hari'] ?? ''),
                        'jumlah_peserta' => (int) ($item['jumlah_peserta'] ?? 0),
                        'persentase_peserta' => (float) ($item['persentase_peserta'] ?? 0),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function transformDetailKetersediaan(): array
    {
        return collect($this['detail_ketersediaan'] ?? [])
            ->map(static function (array $row): array {
                return [
                    'minggu' => (int) ($row['minggu'] ?? 0),
                    'hari' => collect($row['hari'] ?? [])
                        ->map(static function (array $item): array {
                            return [
                                'hari' => (string) ($item['hari'] ?? ''),
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
}
