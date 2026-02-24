<?php

namespace App\Http\Requests;

use App\Models\PesertaHari;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegistrasiRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $lokasi = $this->input('lokasi');

        $payload = [
            'catatan' => $this->normalizeNullableString($this->input('catatan')),
        ];

        if (is_array($lokasi)) {
            $payload['lokasi'] = [
                ...$lokasi,
                'nama_tempat' => $this->normalizeNullableString($lokasi['nama_tempat'] ?? null),
                'alamat' => $this->normalizeNullableString($lokasi['alamat'] ?? null),
                'latitude' => $this->normalizeNullableCoordinate($lokasi['latitude'] ?? null),
                'longitude' => $this->normalizeNullableCoordinate($lokasi['longitude'] ?? null),
                'google_place_id' => $this->normalizeNullableString($lokasi['google_place_id'] ?? null),
            ];
        }

        $this->merge($payload);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_lengkap' => ['required', 'string', 'min:3', 'max:100', "regex:/^[a-zA-Z\\s'.]+$/"],
            'minggu' => ['required', 'integer', 'between:1,4'],
            'hari' => ['required', 'array', 'min:1'],
            'hari.*' => ['required', Rule::in(PesertaHari::HARI_LIST)],
            'budget_per_orang' => ['required', 'integer', 'min:10000', 'max:500000'],
            'catatan' => ['nullable', 'string', 'max:500'],
            'lokasi' => ['required', 'array'],
            'lokasi.nama_tempat' => ['required', 'string', 'min:3', 'max:200'],
            'lokasi.alamat' => ['nullable', 'string', 'max:500'],
            'lokasi.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'lokasi.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'lokasi.google_place_id' => ['nullable', 'string', 'max:100'],
        ];
    }

    private function normalizeNullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized !== '' ? $normalized : null;
    }

    private function normalizeNullableCoordinate(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        return $value;
    }
}
