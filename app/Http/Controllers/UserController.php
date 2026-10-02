<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan daftar anggota
    public function index()
    {
        $users = User::where('role', 'anggota')->latest()->get();
        return view('users.index', compact('users'));
    }

    // Menampilkan form tambah anggota
    public function create()
    {
        return view('users.create');
    }

    // Menyimpan data anggota baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'anggota', // Set default role sebagai anggota
        ]);

        return redirect('/users')->with('success', 'Data anggota berhasil ditambahkan!');
    }

    // Menampilkan form edit anggota
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    // Memproses pembaruan data anggota
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8', // Password opsional saat diubah
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Jika password diisi, update passwordnya
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect('/users')->with('success', 'Data anggota berhasil diperbarui!');
    }

    // Menghapus anggota
    public function destroy($id)
    {
        User::destroy($id);
        return redirect('/users')->with('success', 'Data anggota berhasil dihapus!');
    }
}