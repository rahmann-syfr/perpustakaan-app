<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Menampilkan daftar buku beserta nama kategorinya untuk Admin (Fungsi yang hilang)
    public function index()
    {
        // Menggunakan 'with' untuk memuat relasi kategori (Eager Loading)
        $books = Book::with('category')->latest()->get();
        return view('books.index', compact('books'));
    }

    // Menampilkan halaman form tambah buku
    public function create()
    {
        $categories = Category::all();
        return view('books.create', compact('categories'));
    }

    // Menyimpan data buku ke database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books,isbn',
            'published_year' => 'required|integer',
            'total_stock' => 'required|integer|min:1',
        ]);

        Book::create([
            'title' => $request->title,
            'category_id' => $request->category_id,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'published_year' => $request->published_year,
            'total_stock' => $request->total_stock,
            'available_stock' => $request->total_stock, 
        ]);

        return redirect('/books')->with('success', 'Buku berhasil ditambahkan!');
    }

    // Menghapus buku
    public function destroy($id)
    {
        Book::destroy($id);
        return redirect('/books')->with('success', 'Buku berhasil dihapus!');
    }

    // Menampilkan katalog buku untuk anggota
    public function katalog()
    {
        // Ambil semua buku beserta kategorinya
        $books = Book::with('category')->latest()->get();
        return view('katalog', compact('books'));
    }
}