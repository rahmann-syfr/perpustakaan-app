<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ExtendBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'days' => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'days.required' => 'Jumlah hari perpanjangan wajib diisi.',
            'days.integer' => 'Jumlah hari perpanjangan harus berupa angka bulat.',
            'days.min' => 'Perpanjangan minimal 1 hari.',
            'days.max' => 'Perpanjangan maksimal 30 hari.',
        ];
    }
}
