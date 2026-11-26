<?php

namespace App\Models;

use Database\Factories\BookFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    /**
     * `available_stock` sengaja tidak ada di sini: nilai tersebut diturunkan
     * dari `total_stock` dikurangi jumlah buku yang sedang dipinjam.
     *
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'title',
        'author',
        'publisher',
        'isbn',
        'published_year',
        'total_stock',
        'fine_per_day',
        'cover_image',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_year' => 'integer',
            'total_stock' => 'integer',
            'available_stock' => 'integer',
            'fine_per_day' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Peminjaman yang masih aktif (buku belum dikembalikan).
     */
    public function activeBorrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class)
            ->whereIn('status', ['borrowed', 'overdue'])
            ->whereNull('return_date');
    }

    public function isOutOfStock(): bool
    {
        return $this->available_stock < 1;
    }
}
