<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lokasi_id' => ['required', 'integer', 'exists:lokasi,id'],
            'voter_name' => ['required', 'string', 'min:3', 'max:100', "regex:/^[a-zA-Z\\s'.]+$/"],
            'session_token' => ['nullable', 'string', 'max:64'],
        ];
    }
}
