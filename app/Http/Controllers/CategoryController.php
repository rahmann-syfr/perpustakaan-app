<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Menampilkan halaman daftar kategori
    public function index()
    {
        $categories = Category::latest()->get();
        return view('categories.index', compact('categories'));
    }

    // Menyimpan kategori baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect('/categories')->with('success', 'Kategori berhasil ditambahkan!');
    }

    // Menghapus kategori
    public function destroy($id)
    {
        Category::destroy($id);
        return redirect('/categories')->with('success', 'Kategori berhasil dihapus!');
    }
}