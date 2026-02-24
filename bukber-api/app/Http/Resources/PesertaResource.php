<?php

namespace App\Http\Resources;

use App\Models\PesertaHari;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesertaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $defaultMinggu = (int) ($this->resource->getAttribute('minggu') ?? 1);

        $preferensiMinggu = $this->whenLoaded('hari', function (): array {
            $hariOrder = array_flip(PesertaHari::HARI_LIST);
            $defaultMinggu = (int) ($this->resource->getAttribute('minggu') ?? 1);

            return $this->hari
                ->map(static function ($item) use ($defaultMinggu): array {
                    $minggu = (int) ($item->minggu ?? $defaultMinggu);

                    return [
                        'minggu' => $minggu,
                        'hari' => (string) $item->hari,
                    ];
                })
                ->groupBy('minggu')
                ->sortKeys()
                ->map(static function ($items, $minggu) use ($hariOrder): array {
                    $hari = $items
                        ->pluck('hari')
                        ->unique()
                        ->sort(static fn (string $a, string $b): int => ($hariOrder[$a] ?? 999) <=> ($hariOrder[$b] ?? 999))
                        ->values()
                        ->all();

                    return [
                        'minggu' => (int) $minggu,
                        'hari' => $hari,
                    ];
                })
                ->values()
                ->all();
        }, []);

        $minggu = collect($preferensiMinggu)->pluck('minggu')->filter()->values()->all();
        if ($minggu === []) {
            $minggu = [$defaultMinggu];
        }
        $hari = collect($preferensiMinggu)->flatMap(static fn (array $item): array => $item['hari'])->unique()->values()->all();
        $totalSlotKetersediaan = collect($preferensiMinggu)->sum(static fn (array $item): int => count($item['hari']));
        $totalSlotFromCount = $this->resource->getAttribute('hari_count');

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'nama_lengkap' => $this->nama_lengkap,
            'minggu' => $minggu,
            'hari' => $hari,
            'preferensi_minggu' => $preferensiMinggu,
            'total_slot_ketersediaan' => (int) ($totalSlotFromCount ?? $totalSlotKetersediaan),
            'budget_per_orang' => (int) $this->budget_per_orang,
            'catatan' => $this->catatan,
            'lokasi' => LokasiResource::make($this->whenLoaded('lokasi')),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
