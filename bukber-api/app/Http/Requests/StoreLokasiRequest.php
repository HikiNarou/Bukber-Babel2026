<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLokasiRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nama_tempat' => $this->normalizeNullableString($this->input('nama_tempat')),
            'alamat' => $this->normalizeNullableString($this->input('alamat')),
            'latitude' => $this->normalizeNullableCoordinate($this->input('latitude')),
            'longitude' => $this->normalizeNullableCoordinate($this->input('longitude')),
            'google_place_id' => $this->normalizeNullableString($this->input('google_place_id')),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'peserta_id' => ['nullable', 'integer', 'exists:peserta,id'],
            'nama_tempat' => ['required', 'string', 'min:3', 'max:200'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'google_place_id' => ['nullable', 'string', 'max:100'],
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
