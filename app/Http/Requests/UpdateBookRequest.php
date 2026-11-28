<?php

namespace App\Http\Requests;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
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
        /** @var Book $book */
        $book = $this->route('book');

        $borrowedCopies = $book->activeBorrowings()->count();

        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['nullable', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:255', Rule::unique('books', 'isbn')->ignore($book->id)],
            'published_year' => ['required', 'integer', 'min:1900', 'max:'.(now()->year + 1)],
            'total_stock' => ['required', 'integer', 'min:'.max(1, $borrowedCopies), 'max:999'],
            'fine_per_day' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul buku wajib diisi.',
            'category_id.required' => 'Kategori buku wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'author.required' => 'Nama penulis wajib diisi.',
            'isbn.required' => 'ISBN wajib diisi.',
            'isbn.unique' => 'ISBN tersebut sudah digunakan buku lain.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'published_year.integer' => 'Tahun terbit harus berupa angka.',
            'published_year.min' => 'Tahun terbit tidak valid.',
            'published_year.max' => 'Tahun terbit tidak valid.',
            'total_stock.required' => 'Stok total wajib diisi.',
            'total_stock.min' => 'Stok total tidak boleh lebih kecil dari jumlah buku yang sedang dipinjam.',
            'total_stock.max' => 'Stok total maksimal 999 eksemplar.',
            'fine_per_day.min' => 'Denda per hari tidak boleh negatif.',
            'cover_image.image' => 'Sampul harus berupa gambar.',
            'cover_image.mimes' => 'Format sampul harus JPG, JPEG, PNG, atau WEBP.',
            'cover_image.max' => 'Ukuran sampul maksimal 2MB.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'category_id' => 'kategori',
            'published_year' => 'tahun terbit',
            'total_stock' => 'stok total',
            'fine_per_day' => 'denda per hari',
            'cover_image' => 'sampul buku',
        ];
    }
}
