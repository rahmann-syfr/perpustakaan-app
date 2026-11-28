@extends('layouts.app')

@section('title', 'Tambah Buku - PerpusApp')
@section('page-title', 'Tambah Data Buku')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-lg-9">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center border-bottom pb-3">
                <div>
                    <h4 class="card-title mb-1">Form Tambah Buku Baru</h4>
                    <p class="text-muted small mb-0">Lengkapi detail informasi buku dan unggah gambar sampul jika tersedia.</p>
                </div>
                <a href="{{ route('books.index') }}" class="btn btn-light-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="card-body pt-4">
                <form action="{{ route('books.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   placeholder="Masukkan judul lengkap buku"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penulis <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="author"
                                   class="form-control @error('author') is-invalid @enderror"
                                   placeholder="Nama penulis buku"
                                   value="{{ old('author') }}"
                                   required>
                            @error('author')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penerbit</label>
                            <input type="text"
                                   name="publisher"
                                   class="form-control @error('publisher') is-invalid @enderror"
                                   placeholder="Nama penerbit (opsional)"
                                   value="{{ old('publisher') }}">
                            @error('publisher')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tahun Terbit <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="published_year"
                                   class="form-control @error('published_year') is-invalid @enderror"
                                   placeholder="Contoh: {{ date('Y') }}"
                                   value="{{ old('published_year') }}"
                                   min="1900"
                                   max="{{ date('Y') }}"
                                   required>
                            @error('published_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nomor ISBN <span class="text-danger">*</span></label>
                            <input type="text"
                                   name="isbn"
                                   class="form-control @error('isbn') is-invalid @enderror"
                                   placeholder="Contoh: 978-602-xxx-x"
                                   value="{{ old('isbn') }}"
                                   required>
                            @error('isbn')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Total Stok <span class="text-danger">*</span></label>
                            <input type="number"
                                   name="total_stock"
                                   class="form-control @error('total_stock') is-invalid @enderror"
                                   value="{{ old('total_stock', 1) }}"
                                   min="1"
                                   max="999"
                                   required>
                            <small class="text-muted">Maksimal 999 eksemplar per judul.</small>
                            @error('total_stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Cover Buku (Opsional)</label>
                            <div class="d-flex flex-column flex-sm-row align-items-start gap-3 p-3 border rounded bg-light">
                                <div id="previewContainer" class="flex-shrink-0">
                                    <div id="previewPlaceholder" class="bg-white border rounded d-flex flex-column justify-content-center align-items-center text-muted" style="width: 85px; height: 115px;">
                                        <i class="bi bi-image fs-3 opacity-50"></i>
                                        <small style="font-size: 0.65rem;">Preview</small>
                                    </div>
                                    <img id="coverPreview" src="" alt="Preview Sampul" class="rounded shadow-sm d-none" width="85" height="115" style="object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 w-100">
                                    <input type="file"
                                           name="cover_image"
                                           id="coverInput"
                                           class="form-control @error('cover_image') is-invalid @enderror"
                                           accept="image/jpeg,image/png,image/jpg">
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle me-1"></i>Format: JPG, JPEG, atau PNG. Ukuran maksimal 2MB.
                                    </small>
                                    @error('cover_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('books.index') }}" class="btn btn-light-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Simpan Buku
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('coverInput')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('coverPreview');
        const placeholder = document.getElementById('previewPlaceholder');

        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.src = event.target.result;
                previewImg.classList.remove('d-none');
                placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.classList.add('d-none');
            placeholder.classList.remove('d-none');
        }
    });
</script>
@endpush