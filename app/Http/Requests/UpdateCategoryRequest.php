<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'parent_id' => [
                'nullable', 
                'string', 
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    if ($value === $this->route('category')) {
                        $fail('Kategori tidak boleh menjadi induk dari dirinya sendiri.');
                    }
                }
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi jika disertakan.',
            'parent_id.exists' => 'Kategori induk (parent) tidak valid.',
        ];
    }
}
