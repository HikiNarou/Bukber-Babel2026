<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TanggalFinalResource;
use App\Models\Peserta;
use App\Models\TanggalFinal;
use Illuminate\Support\Facades\Cache;

class TanggalController extends Controller
{
    public function show()
    {
        $payload = Cache::remember('tanggal_final', 300, function (): array {
            $tanggalFinal = TanggalFinal::query()->with('lokasi')->latest('id')->first();

            if (! $tanggalFinal) {
                return [
                    'is_locked' => false,
                    'tanggal' => null,
                    'hari' => null,
                    'jam' => null,
                    'lokasi' => null,
                    'estimasi_budget' => (int) (Peserta::query()->avg('budget_per_orang') ?? 0),
                    'total_peserta' => Peserta::query()->count(),
                    'catatan' => null,
                ];
            }

            $resource = TanggalFinalResource::make($tanggalFinal)->resolve();

            return [
                ...$resource,
                'estimasi_budget' => (int) (Peserta::query()->avg('budget_per_orang') ?? 0),
                'total_peserta' => Peserta::query()->count(),
            ];
        });

        return $this->success($payload, 'Tanggal final berhasil diambil.');
    }
}
