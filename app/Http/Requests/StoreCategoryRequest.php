<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'string', 'exists:categories,id'],
            'passing_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'parent_id.exists' => 'Kategori induk (parent) tidak valid.',
            'passing_grade.numeric' => 'KKM kategori harus berupa angka.',
            'passing_grade.min' => 'KKM kategori tidak boleh kurang dari 0.',
            'passing_grade.max' => 'KKM kategori tidak boleh lebih dari 100.',
        ];
    }
}
