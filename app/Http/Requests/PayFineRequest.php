<?php

namespace App\Http\Requests;

use App\Actions\PayFine;
use App\Enums\PaymentMethod;
use App\Models\Borrowing;
use App\Models\FinePayment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayFineRequest extends FormRequest
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
            'amount' => ['required', 'integer', 'min:1', 'max:100000000'],
            'method' => ['required', Rule::enum(PaymentMethod::class)],
            'reference' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Nominal pembayaran wajib diisi.',
            'amount.integer' => 'Nominal pembayaran harus berupa angka.',
            'amount.min' => 'Nominal pembayaran minimal Rp1.',
            'method.required' => 'Metode pembayaran wajib dipilih.',
            'method.enum' => 'Metode pembayaran tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'amount' => 'nominal pembayaran',
            'method' => 'metode pembayaran',
            'reference' => 'referensi',
            'note' => 'catatan',
        ];
    }

    /**
     * Mencatat pembayaran denda pada transaksi terkait.
     */
    public function payFine(Borrowing $borrowing): FinePayment
    {
        return app(PayFine::class)->handle(
            $borrowing,
            $this->integer('amount'),
            PaymentMethod::from($this->string('method')->toString()),
            $this->user()->id,
            $this->filled('reference') ? $this->string('reference')->toString() : null,
            $this->filled('note') ? $this->string('note')->toString() : null,
        );
    }
}
