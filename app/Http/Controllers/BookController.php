<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Category;
use App\Models\SystemSetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku dengan pencarian dan filter kategori.
     */
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $books = Book::with('category')
            ->when(
                $filters['search'] ?? null,
                fn (Builder $query, string $search) => $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                })
            )
            ->when(
                $filters['category'] ?? null,
                fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('books', 'categories'));
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Book $book): View
    {
        $book->load('category');

        return view('books.show', compact('book'));
    }

    /**
     * Menampilkan form tambah buku.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.create', compact('categories'));
    }

    /**
     * Menyimpan buku baru.
     */
    public function store(StoreBookRequest $request): RedirectResponse
    {
        $book = new Book([
            'category_id' => $request->integer('category_id'),
            'title' => $request->string('title')->toString(),
            'author' => $request->string('author')->toString(),
            'publisher' => $request->filled('publisher') ? $request->string('publisher')->toString() : null,
            'isbn' => $request->string('isbn')->toString(),
            'published_year' => $request->integer('published_year'),
            'total_stock' => $request->integer('total_stock'),
            'fine_per_day' => $request->filled('fine_per_day')
                ? $request->integer('fine_per_day')
                : SystemSetting::getInt('denda_per_hari', 1000),
            'cover_image' => $this->storeCoverImage($request),
        ]);

        // Stok tersedia selalu sama dengan stok total saat buku baru dibuat.
        $book->available_stock = $book->total_stock;
        $book->save();

        return redirect()->route('books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit buku.
     */
    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();

        return view('books.edit', compact('book', 'categories'));
    }

    /**
     * Memperbarui data buku.
     */
    public function update(UpdateBookRequest $request, Book $book): RedirectResponse
    {
        $coverImagePath = $book->cover_image;

        if ($request->hasFile('cover_image')) {
            $this->deleteCoverImage($book);
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        $borrowedCopies = $book->activeBorrowings()->count();

        $book->fill([
            'category_id' => $request->integer('category_id'),
            'title' => $request->string('title')->toString(),
            'author' => $request->string('author')->toString(),
            'publisher' => $request->filled('publisher') ? $request->string('publisher')->toString() : null,
            'isbn' => $request->string('isbn')->toString(),
            'published_year' => $request->integer('published_year'),
            'total_stock' => $request->integer('total_stock'),
            'fine_per_day' => $request->filled('fine_per_day')
                ? $request->integer('fine_per_day')
                : $book->fine_per_day,
            'cover_image' => $coverImagePath,
        ]);

        // Stok tersedia diturunkan dari stok total dikurangi buku yang sedang dipinjam
        // sehingga tidak pernah menjadi negatif.
        $book->available_stock = max(0, $book->total_stock - $borrowedCopies);
        $book->save();

        return redirect()->route('books.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * Menghapus buku.
     */
    public function destroy(Book $book): RedirectResponse
    {
        if ($book->activeBorrowings()->exists()) {
            return redirect()->route('books.index')
                ->with('error', 'Buku masih dipinjam sehingga tidak dapat dihapus.');
        }

        $this->deleteCoverImage($book);
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Buku beserta sampulnya berhasil dihapus!');
    }

    /**
     * Menyimpan file sampul buku.
     */
    private function storeCoverImage(Request $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        return $request->file('cover_image')->store('covers', 'public');
    }

    /**
     * Menghapus file sampul buku.
     */
    private function deleteCoverImage(Book $book): void
    {
        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }
    }
}
