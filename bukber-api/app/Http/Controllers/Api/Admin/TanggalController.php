<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTanggalRequest;
use App\Http\Resources\TanggalFinalResource;
use App\Models\TanggalFinal;
use Illuminate\Support\Facades\Cache;

class TanggalController extends Controller
{
    public function store(StoreTanggalRequest $request)
    {
        $tanggal = TanggalFinal::query()->create([
            ...$request->validated(),
            'is_locked' => $request->boolean('is_locked', true),
        ]);

        Cache::forget('tanggal_final');

        return $this->success(
            TanggalFinalResource::make($tanggal->load('lokasi')),
            'Tanggal final berhasil dibuat.',
            201
        );
    }

    public function update(StoreTanggalRequest $request)
    {
        $tanggal = TanggalFinal::query()->latest('id')->first();

        if (! $tanggal) {
            return $this->error('Tanggal final belum dibuat.', null, 404);
        }

        $tanggal->fill($request->validated());
        $tanggal->is_locked = $request->boolean('is_locked', $tanggal->is_locked);
        $tanggal->save();

        Cache::forget('tanggal_final');

        return $this->success(
            TanggalFinalResource::make($tanggal->load('lokasi')),
            'Tanggal final berhasil diperbarui.'
        );
    }
}
