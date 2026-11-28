<?php

namespace App\Http\Requests;

use App\Enums\BorrowingStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReturnBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in([BorrowingStatus::Returned->value, BorrowingStatus::Lost->value]),
            ],
            'lost_fine_amount' => ['nullable', 'integer', 'min:0', 'max:100000000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Status pengembalian wajib dipilih.',
            'status.in' => 'Status pengembalian tidak valid.',
            'lost_fine_amount.integer' => 'Denda buku hilang harus berupa angka.',
            'lost_fine_amount.min' => 'Denda buku hilang tidak boleh negatif.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'status' => 'status pengembalian',
            'lost_fine_amount' => 'denda buku hilang',
        ];
    }
}
