<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TanggalFinalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $tanggal = $this->tanggal ? Carbon::parse($this->tanggal) : null;

        return [
            'is_locked' => (bool) $this->is_locked,
            'tanggal' => $tanggal?->format('Y-m-d'),
            'hari' => $tanggal?->locale('id')->translatedFormat('l'),
            'jam' => $this->jam,
            'lokasi' => LokasiResource::make($this->whenLoaded('lokasi')),
            'catatan' => $this->catatan,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
