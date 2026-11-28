<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role->canManageUsers() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'maksimal_hari_pinjam' => ['required', 'integer', 'min:1', 'max:90'],
            'nama_perpustakaan' => ['required', 'string', 'max:120'],
            'alamat_perpustakaan' => ['nullable', 'string', 'max:255'],
            'email_perpustakaan' => ['nullable', 'email', 'max:120'],
            'telepon_perpustakaan' => ['nullable', 'string', 'max:30'],
            'maksimal_buku_anggota' => ['required', 'integer', 'min:1', 'max:50'],
            'hari_pengingat' => ['required', 'integer', 'min:0', 'max:30'],
            'maksimal_perpanjangan' => ['required', 'integer', 'min:0', 'max:10'],
            'durasi_perpanjangan' => ['required', 'integer', 'min:1', 'max:90'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'maksimal_hari_pinjam.required' => 'Maksimal durasi pinjam wajib diisi.',
            'maksimal_hari_pinjam.max' => 'Maksimal durasi pinjam maksimal 90 hari.',
            'nama_perpustakaan.required' => 'Nama perpustakaan wajib diisi.',
            'email_perpustakaan.email' => 'Format email perpustakaan tidak valid.',
            'maksimal_buku_anggota.required' => 'Maksimal buku per anggota wajib diisi.',
            'hari_pengingat.required' => 'Jangka waktu pengingat wajib diisi.',
            'maksimal_perpanjangan.required' => 'Maksimal perpanjangan wajib diisi.',
            'durasi_perpanjangan.required' => 'Durasi perpanjangan wajib diisi.',
        ];
    }
}
