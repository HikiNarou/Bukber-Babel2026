<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VotingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lokasi_id' => $this->lokasi_id,
            'voter_name' => $this->voter_name,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'lokasi' => LokasiResource::make($this->whenLoaded('lokasi')),
        ];
    }
}
