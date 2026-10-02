<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->latest()->get();
        return view('books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'published_year' => 'required|integer',
            'total_stock' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $coverImagePath = null;
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        Book::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_year' => $request->published_year,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->total_stock, 
            'cover_image' => $coverImagePath,
        ]);

        return redirect('/books')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Menampilkan halaman form edit
    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('books.edit', compact('book', 'categories'));
    }

    // Memproses pembaruan data ke database
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn,' . $id, 
            'published_year' => 'required|integer',
            'total_stock' => 'required|integer|min:1',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
        ]);

        $coverImagePath = $book->cover_image; 

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $coverImagePath = $request->file('cover_image')->store('covers', 'public');
        }

        $stockDifference = $request->total_stock - $book->total_stock;
        $newAvailableStock = $book->available_stock + $stockDifference;

        $book->update([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_year' => $request->published_year,
            'total_stock' => $request->total_stock,
            'available_stock' => $newAvailableStock,
            'cover_image' => $coverImagePath,
        ]);

        return redirect('/books')->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover_image && Storage::disk('public')->exists($book->cover_image)) {
            Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();
        return redirect('/books')->with('success', 'Buku beserta sampulnya berhasil dihapus!');
    }

    public function katalog(Request $request)
    {
        $query = Book::with('category')->latest();

        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $books = $query->get();
        $categories = Category::all();

        return view('katalog', compact('books', 'categories'));
    }
}