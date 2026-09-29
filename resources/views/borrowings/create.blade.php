@extends('layouts.app')

@section('title', 'Proses Peminjaman - PerpusApp')
@section('page-title', 'Form Peminjaman Buku')

@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Input Transaksi Baru</h4>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="/borrowings" method="POST">
                    @csrf
                    <div class="form-group mb-4">
                        <label for="user_id" class="mb-2">Anggota Peminjam</label>
                        <select id="user_id" name="user_id" class="form-select" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label for="book_id" class="mb-2">Buku yang Dipinjam</label>
                        <select id="book_id" name="book_id" class="form-select" required>
                            <option value="">-- Pilih Buku --</option>
                            @foreach($books as $book)
                                <option value="{{ $book->id }}">
                                    {{ $book->title }} - Stok: {{ $book->available_stock }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block"><i class="bi bi-info-circle"></i> Hanya buku dengan stok tersedia yang muncul di sini.</small>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <a href="/borrowings" class="btn btn-light-secondary me-2">Batal</a>
                        <button type="submit" class="btn btn-primary">Proses Peminjaman</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection