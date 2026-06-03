<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProctoringLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_type' => ['required', 'string', 'max:255'],
            'event_details' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_type.required' => 'Tipe kejadian pengawasan wajib disertakan.',
        ];
    }
}
