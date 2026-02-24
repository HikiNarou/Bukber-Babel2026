<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LokasiResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_tempat' => $this->nama_tempat,
            'alamat' => $this->alamat,
            'latitude' => $this->latitude !== null ? (float) $this->latitude : null,
            'longitude' => $this->longitude !== null ? (float) $this->longitude : null,
            'google_place_id' => $this->google_place_id,
            'total_votes' => isset($this->votes_count) ? (int) $this->votes_count : (int) ($this->total_votes ?? 0),
            'percentage' => isset($this->percentage) ? (float) $this->percentage : null,
            'created_at' => optional($this->created_at)->toIso8601String(),
        ];
    }
}
