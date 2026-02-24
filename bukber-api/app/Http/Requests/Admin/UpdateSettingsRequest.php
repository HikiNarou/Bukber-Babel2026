<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_event' => ['nullable', 'string', 'max:200'],
            'deadline_registrasi' => ['nullable', 'date'],
            'deadline_voting' => ['nullable', 'date'],
            'is_registration_open' => ['nullable', 'boolean'],
            'is_voting_open' => ['nullable', 'boolean'],
        ];
    }
}
