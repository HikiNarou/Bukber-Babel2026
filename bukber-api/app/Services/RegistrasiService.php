<?php

namespace App\Services;

use App\Models\EventSetting;
use App\Models\Peserta;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegistrasiService
{
    public function store(array $payload, ?string $deviceFingerprint, ?string $ipAddress): Peserta
    {
        $this->ensureRegistrationOpen();
        $this->ensureUniquePeserta($payload['nama_lengkap'], $deviceFingerprint, $ipAddress);

        $peserta = DB::transaction(function () use ($payload, $deviceFingerprint, $ipAddress): Peserta {
            $peserta = Peserta::query()->create([
                'nama_lengkap' => trim($payload['nama_lengkap']),
                'minggu' => $payload['minggu'],
                'budget_per_orang' => $payload['budget_per_orang'],
                'catatan' => $this->normalizeNullableString($payload['catatan'] ?? null),
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $ipAddress,
            ]);

            $hariPayload = collect($payload['hari'])
                ->map(static fn (string $hari): string => trim(strtolower($hari)))
                ->unique()
                ->values();

            $peserta->hari()->createMany(
                $hariPayload->map(static fn (string $hari): array => ['hari' => $hari])->all()
            );

            $peserta->lokasi()->create([
                'nama_tempat' => trim((string) $payload['lokasi']['nama_tempat']),
                'alamat' => $this->normalizeNullableString($payload['lokasi']['alamat'] ?? null),
                'latitude' => $payload['lokasi']['latitude'] ?? null,
                'longitude' => $payload['lokasi']['longitude'] ?? null,
                'google_place_id' => $this->normalizeNullableString($payload['lokasi']['google_place_id'] ?? null),
            ]);

            return $peserta;
        });

        $this->clearDashboardCaches();

        return $peserta->load(['hari', 'lokasi']);
    }

    public function update(Peserta $peserta, array $payload, ?string $deviceFingerprint, ?string $ipAddress): Peserta
    {
        $this->ensureRegistrationOpen();
        $this->ensureUniquePeserta(
            $payload['nama_lengkap'],
            $deviceFingerprint,
            $ipAddress,
            $peserta->id
        );

        DB::transaction(function () use ($peserta, $payload, $deviceFingerprint, $ipAddress): void {
            $peserta->update([
                'nama_lengkap' => trim($payload['nama_lengkap']),
                'minggu' => $payload['minggu'],
                'budget_per_orang' => $payload['budget_per_orang'],
                'catatan' => $this->normalizeNullableString($payload['catatan'] ?? null),
                'device_fingerprint' => $deviceFingerprint ?? $peserta->device_fingerprint,
                'ip_address' => $ipAddress ?? $peserta->ip_address,
            ]);

            $peserta->hari()->delete();

            $hariPayload = collect($payload['hari'])
                ->map(static fn (string $hari): string => trim(strtolower($hari)))
                ->unique()
                ->values();

            $peserta->hari()->createMany(
                $hariPayload->map(static fn (string $hari): array => ['hari' => $hari])->all()
            );

            if ($peserta->lokasi) {
                $peserta->lokasi->update([
                    'nama_tempat' => trim((string) $payload['lokasi']['nama_tempat']),
                    'alamat' => $this->normalizeNullableString($payload['lokasi']['alamat'] ?? null),
                    'latitude' => $payload['lokasi']['latitude'] ?? null,
                    'longitude' => $payload['lokasi']['longitude'] ?? null,
                    'google_place_id' => $this->normalizeNullableString($payload['lokasi']['google_place_id'] ?? null),
                ]);
            } else {
                $peserta->lokasi()->create([
                    'nama_tempat' => trim((string) $payload['lokasi']['nama_tempat']),
                    'alamat' => $this->normalizeNullableString($payload['lokasi']['alamat'] ?? null),
                    'latitude' => $payload['lokasi']['latitude'] ?? null,
                    'longitude' => $payload['lokasi']['longitude'] ?? null,
                    'google_place_id' => $this->normalizeNullableString($payload['lokasi']['google_place_id'] ?? null),
                ]);
            }
        });

        $this->clearDashboardCaches();

        return $peserta->fresh(['hari', 'lokasi']);
    }

    private function ensureRegistrationOpen(): void
    {
        $settings = EventSetting::singleton();
        $deadline = $settings->deadline_registrasi;

        if (! $settings->is_registration_open || ($deadline && now()->greaterThan($deadline))) {
            throw ValidationException::withMessages([
                'registrasi' => ['Pendaftaran sudah ditutup.'],
            ]);
        }
    }

    private function ensureUniquePeserta(
        string $namaLengkap,
        ?string $deviceFingerprint,
        ?string $ipAddress,
        ?int $excludeId = null
    ): void {
        $query = Peserta::query()->whereRaw('LOWER(nama_lengkap) = ?', [mb_strtolower(trim($namaLengkap))]);

        if ($excludeId !== null) {
            $query->where('id', '!=', $excludeId);
        }

        if ($deviceFingerprint || $ipAddress) {
            $query->where(function ($sub) use ($deviceFingerprint, $ipAddress): void {
                if ($deviceFingerprint) {
                    $sub->orWhere('device_fingerprint', $deviceFingerprint);
                }

                if ($ipAddress) {
                    $sub->orWhere('ip_address', $ipAddress);
                }
            });
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'nama_lengkap' => ['Pendaftaran duplikat terdeteksi untuk nama/perangkat/jaringan yang sama.'],
            ]);
        }
    }

    private function clearDashboardCaches(): void
    {
        foreach ([
            'dashboard_stats',
            'chart_hari',
            'chart_minggu',
            'chart_budget',
            'responden_latest',
            'list_lokasi',
            'voting_overview',
            'tanggal_final',
        ] as $key) {
            Cache::forget($key);
        }
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }
}
