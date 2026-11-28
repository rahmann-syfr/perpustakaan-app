@php
    $isEdit = isset($book) && $book !== null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <x-ui.input
            name="title"
            label="Judul Buku"
            :value="$isEdit ? $book->title : null"
            placeholder="Masukkan judul lengkap buku"
            :required="true"
        />
    </div>

    <x-ui.input name="category_id" type="select" label="Kategori" :required="true">
        <option value="">-- Pilih Kategori --</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ (string) old('category_id', $isEdit ? $book->category_id : '') === (string) $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </x-ui.input>

    <x-ui.input
        name="author"
        label="Penulis"
        :value="$isEdit ? $book->author : null"
        placeholder="Nama penulis buku"
        :required="true"
    />

    <x-ui.input
        name="publisher"
        label="Penerbit"
        :value="$isEdit ? $book->publisher : null"
        placeholder="Nama penerbit (opsional)"
    />

    <x-ui.input
        name="published_year"
        type="number"
        label="Tahun Terbit"
        :value="$isEdit ? $book->published_year : null"
        :placeholder="'Contoh: ' . date('Y')"
        min="1900"
        :max="date('Y')"
        :required="true"
    />

    <x-ui.input
        name="isbn"
        label="Nomor ISBN"
        :value="$isEdit ? $book->isbn : null"
        placeholder="Contoh: 978-602-xxx-x"
        :required="!$isEdit"
    />

    <x-ui.input
        name="total_stock"
        type="number"
        label="Total Stok"
        :value="$isEdit ? $book->total_stock : 1"
        min="1"
        max="999"
        hint="Maksimal 999 eksemplar per judul."
        :required="true"
    />

    <div class="md:col-span-2 space-y-2">
        <label for="coverInput" class="font-mono text-[12px] font-bold uppercase tracking-[0.04em] text-[#0A0A0A] block">
            {{ $isEdit ? 'Sampul Buku (Biarkan kosong jika tidak diubah)' : 'Sampul Buku (Opsional)' }}
        </label>

        <div class="flex flex-col sm:flex-row items-start gap-4 p-4 border-[3px] border-[#0A0A0A] bg-[#FFF6E0] neo-shadow-sm">
            <div class="shrink-0">
                @if ($isEdit && $book->cover_image)
                    <img
                        id="coverPreview"
                        src="{{ asset('storage/' . $book->cover_image) }}"
                        alt="Sampul Saat Ini"
                        class="w-24 h-32 object-cover border-[3px] border-[#0A0A0A] neo-shadow-sm"
                    />
                    <div id="previewPlaceholder" class="hidden w-24 h-32 bg-white border-[3px] border-[#0A0A0A] flex-col items-center justify-center text-[#4f4633]">
                        <span class="material-symbols-outlined text-[28px]">image</span>
                        <span class="font-mono text-[10px] font-bold uppercase">Preview</span>
                    </div>
                @else
                    <div id="previewPlaceholder" class="w-24 h-32 bg-white border-[3px] border-[#0A0A0A] flex flex-col items-center justify-center text-[#4f4633]">
                        <span class="material-symbols-outlined text-[28px]">image</span>
                        <span class="font-mono text-[10px] font-bold uppercase">Preview</span>
                    </div>
                    <img
                        id="coverPreview"
                        src=""
                        alt="Preview Sampul"
                        class="hidden w-24 h-32 object-cover border-[3px] border-[#0A0A0A] neo-shadow-sm"
                    />
                @endif
            </div>

            <div class="flex-1 w-full space-y-2">
                <input
                    type="file"
                    name="cover_image"
                    id="coverInput"
                    accept="image/jpeg,image/png,image/jpg"
                    class="w-full bg-white text-[#0A0A0A] font-sans text-[14px] px-3 py-2.5 border-[3px] border-[#0A0A0A] neo-shadow-sm cursor-pointer"
                />
                <p class="font-sans text-[12px] text-[#4f4633] m-0">
                    Format: JPG, JPEG, atau PNG. Ukuran maksimal 2MB.
                </p>
                @error('cover_image')
                    <p class="font-mono text-[11px] font-bold text-[#93000a] bg-[#ffdad6] border-[2px] border-[#0A0A0A] px-2.5 py-1 m-0">
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('coverInput')?.addEventListener('change', function (e) {
        const file = e.target.files[0];
        const previewImg = document.getElementById('coverPreview');
        const placeholder = document.getElementById('previewPlaceholder');

        if (!previewImg || !placeholder) return;

        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.src = event.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
