<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        $books = [
            [
                'category_id' => 1,
                'title' => 'Pemrograman PHP',
                'author' => 'Andi',
                'isbn' => '978-602-111-1',
                'published_year' => 2024,
                'total_stock' => 5,
                'available_stock' => 5,
            ],
            [
                'category_id' => 1,
                'title' => 'Laravel untuk Pemula',
                'author' => 'Budi',
                'isbn' => '978-602-222-2',
                'published_year' => 2023,
                'total_stock' => 10,
                'available_stock' => 10,
            ],
            [
                'category_id' => 2,
                'title' => 'Basis Data Relasional',
                'author' => 'Citra',
                'isbn' => '978-602-333-3',
                'published_year' => 2022,
                'total_stock' => 7,
                'available_stock' => 7,
            ],
            [
                'category_id' => 3,
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'isbn' => '978-602-444-4',
                'published_year' => 2005,
                'total_stock' => 3,
                'available_stock' => 3,
            ],
            [
                'category_id' => 4,
                'title' => 'Kecerdasan Buatan',
                'author' => 'Diana',
                'isbn' => '978-602-555-5',
                'published_year' => 2025,
                'total_stock' => 8,
                'available_stock' => 8,
            ]
        ];

        // Memasukkan data ke dalam database melalui Eloquent ORM
        foreach ($books as $book) {
            Book::create($book);
        }
    }
}