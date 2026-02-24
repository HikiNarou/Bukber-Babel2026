<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreTanggalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date', 'after_or_equal:today'],
            'jam' => ['nullable', 'date_format:H:i'],
            'lokasi_id' => ['nullable', 'integer', 'exists:lokasi,id'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'is_locked' => ['nullable', 'boolean'],
        ];
    }
}
