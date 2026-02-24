<?php

namespace App\Services;

use App\Models\EventSetting;
use App\Models\Peserta;
use App\Models\PesertaHari;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class RegistrasiService
{
    private ?bool $hasPesertaHariMingguColumn = null;

    public function store(array $payload, ?string $deviceFingerprint, ?string $ipAddress): Peserta
    {
        $this->ensureRegistrationOpen();
        $this->ensureUniquePeserta($payload['nama_lengkap'], $deviceFingerprint, $ipAddress);

        $peserta = DB::transaction(function () use ($payload, $deviceFingerprint, $ipAddress): Peserta {
            $preferensiMinggu = $this->normalizePreferensiMinggu($payload['preferensi_minggu'] ?? []);
            $primaryMinggu = (int) ($preferensiMinggu->first()['minggu'] ?? 1);

            $peserta = Peserta::query()->create([
                'nama_lengkap' => trim($payload['nama_lengkap']),
                'minggu' => $primaryMinggu,
                'budget_per_orang' => $payload['budget_per_orang'],
                'catatan' => $this->normalizeNullableString($payload['catatan'] ?? null),
                'device_fingerprint' => $deviceFingerprint,
                'ip_address' => $ipAddress,
            ]);

            $peserta->hari()->createMany($this->buildHariRows(
                $preferensiMinggu,
                $this->hasMingguOnPesertaHari()
            ));

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
        $preferensiMinggu = $this->normalizePreferensiMinggu($payload['preferensi_minggu'] ?? []);

        DB::transaction(function () use ($peserta, $payload, $deviceFingerprint, $ipAddress, $preferensiMinggu): void {
            $primaryMinggu = (int) ($preferensiMinggu->first()['minggu'] ?? $peserta->minggu ?? 1);

            $peserta->update([
                'nama_lengkap' => trim($payload['nama_lengkap']),
                'minggu' => $primaryMinggu,
                'budget_per_orang' => $payload['budget_per_orang'],
                'catatan' => $this->normalizeNullableString($payload['catatan'] ?? null),
                'device_fingerprint' => $deviceFingerprint ?? $peserta->device_fingerprint,
                'ip_address' => $ipAddress ?? $peserta->ip_address,
            ]);

            $peserta->hari()->delete();
            $peserta->hari()->createMany($this->buildHariRows(
                $preferensiMinggu,
                $this->hasMingguOnPesertaHari()
            ));

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

    private function normalizePreferensiMinggu(array $raw): \Illuminate\Support\Collection
    {
        return collect($raw)
            ->filter(static fn (mixed $item): bool => is_array($item))
            ->map(function (array $item): array {
                $minggu = (int) ($item['minggu'] ?? 0);
                $hari = collect($item['hari'] ?? [])
                    ->filter(static fn (mixed $day): bool => is_string($day))
                    ->map(static fn (string $day): string => trim(strtolower($day)))
                    ->filter(static fn (string $day): bool => in_array($day, PesertaHari::HARI_LIST, true))
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'minggu' => $minggu,
                    'hari' => $hari,
                ];
            })
            ->filter(static fn (array $item): bool => in_array($item['minggu'], PesertaHari::MINGGU_LIST, true) && $item['hari'] !== [])
            ->unique('minggu')
            ->sortBy('minggu')
            ->values();
    }

    private function buildHariRows(
        \Illuminate\Support\Collection $preferensiMinggu,
        bool $includeMinggu
    ): array
    {
        if (! $includeMinggu) {
            return $preferensiMinggu
                ->flatMap(static fn (array $item): array => $item['hari'])
                ->unique()
                ->map(static fn (string $hari): array => ['hari' => $hari])
                ->values()
                ->all();
        }

        return $preferensiMinggu
            ->flatMap(static function (array $item): array {
                return collect($item['hari'])
                    ->map(static fn (string $hari): array => [
                        'minggu' => $item['minggu'],
                        'hari' => $hari,
                    ])
                    ->all();
            })
            ->values()
            ->all();
    }

    private function hasMingguOnPesertaHari(): bool
    {
        if ($this->hasPesertaHariMingguColumn === null) {
            $this->hasPesertaHariMingguColumn = Schema::hasColumn('peserta_hari', 'minggu');
        }

        return $this->hasPesertaHariMingguColumn;
    }
}
