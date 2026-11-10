<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        Category::create(['name' => $request->name]);
        return redirect('/categories')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    // Fungsi menyimpan perubahan edit dari Modal
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::findOrFail($id);
        $category->update(['name' => $request->name]);
        
        return redirect('/categories')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return redirect('/categories')->with('success', 'Kategori berhasil dihapus!');
    }
}