<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PesertaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'nama_lengkap' => $this->nama_lengkap,
            'minggu' => (int) $this->minggu,
            'hari' => $this->whenLoaded('hari', fn () => $this->hari->pluck('hari')->values()->all(), []),
            'budget_per_orang' => (int) $this->budget_per_orang,
            'catatan' => $this->catatan,
            'lokasi' => LokasiResource::make($this->whenLoaded('lokasi')),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
