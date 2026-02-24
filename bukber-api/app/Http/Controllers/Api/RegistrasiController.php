<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRegistrasiRequest;
use App\Http\Requests\UpdateRegistrasiRequest;
use App\Http\Resources\PesertaResource;
use App\Models\Peserta;
use App\Services\RegistrasiService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RegistrasiController extends Controller
{
    public function __construct(private readonly RegistrasiService $registrasiService) {}

    public function index(Request $request)
    {
        $perPage = min((int) $request->integer('per_page', 20), 100);
        $paginator = Peserta::query()
            ->with(['hari', 'lokasi'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return $this->paginated(
            $paginator,
            PesertaResource::collection($paginator->getCollection()),
            'Daftar peserta berhasil diambil.'
        );
    }

    public function store(StoreRegistrasiRequest $request)
    {
        try {
            $peserta = $this->registrasiService->store(
                $request->validated(),
                $request->header('X-Device-Fingerprint') ?: $request->string('device_fingerprint')->toString(),
                $request->ip()
            );
        } catch (ValidationException $exception) {
            return $this->error('Validasi gagal', $exception->errors(), 422);
        }

        return $this->success(
            PesertaResource::make($peserta),
            'Pendaftaran berhasil! Jazakallahu khairan.',
            201
        );
    }

    public function show(string $uuid)
    {
        $peserta = Peserta::query()
            ->with(['hari', 'lokasi'])
            ->where('uuid', $uuid)
            ->first();

        if (! $peserta) {
            return $this->error('Data peserta tidak ditemukan.', null, 404);
        }

        return $this->success(PesertaResource::make($peserta), 'Detail peserta berhasil diambil.');
    }

    public function update(UpdateRegistrasiRequest $request, string $uuid)
    {
        $peserta = Peserta::query()->where('uuid', $uuid)->first();

        if (! $peserta) {
            return $this->error('Data peserta tidak ditemukan.', null, 404);
        }

        try {
            $updated = $this->registrasiService->update(
                $peserta,
                $request->validated(),
                $request->header('X-Device-Fingerprint') ?: $request->string('device_fingerprint')->toString(),
                $request->ip()
            );
        } catch (ValidationException $exception) {
            return $this->error('Validasi gagal', $exception->errors(), 422);
        }

        return $this->success(PesertaResource::make($updated), 'Pendaftaran berhasil diperbarui.');
    }
}
