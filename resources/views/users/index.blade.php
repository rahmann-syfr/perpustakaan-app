@extends('layouts.app')

@section('title', 'Data User - PerpusApp')
@section('page-title', 'Manajemen User')

@section('content')
@php
    $currentUser = Auth::user();
@endphp

<div class="space-y-6">
    <x-ui.card :window="true" title="users.index" bodyClass="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-headline-md text-[22px] font-extrabold text-[#0A0A0A] m-0">Daftar Pengguna Sistem</h2>
                <p class="font-sans text-[14px] text-[#4f4633] mt-1 mb-0">
                    Kelola akun staf perpustakaan (Admin dan Petugas).
                </p>
            </div>
            <x-ui.button variant="primary" icon="person_add" href="{{ route('users.create') }}">
                Tambah User
            </x-ui.button>
        </div>
    </x-ui.card>

    @if ($users->isNotEmpty())
        <div class="bg-white border-[3px] border-[#0A0A0A] neo-shadow-md p-4">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                <div class="md:col-span-6">
                    <x-ui.input
                        name="search_client"
                        id="tableSearch"
                        label="Pencarian"
                        placeholder="Cari nama atau email pengguna..."
                    />
                </div>
                <div class="md:col-span-4">
                    <x-ui.input name="role_filter" id="userRoleFilter" type="select" label="Filter Role">
                        <option value="">Semua Role</option>
                        @foreach (\App\Enums\Role::cases() as $roleCase)
                            <option value="{{ $roleCase->value }}">{{ $roleCase->label() }}</option>
                        @endforeach
                    </x-ui.input>
                </div>
                <div class="md:col-span-2 flex items-center justify-end">
                    <span id="userCounter" class="font-mono text-[11px] font-bold uppercase text-[#4f4633]">
                        {{ $users->count() }} pengguna
                    </span>
                </div>
            </div>
        </div>

        <x-ui.table :headers="['No', 'Pengguna', 'Email', 'Role', 'Terdaftar', 'Aksi']" id="usersTable">
            @foreach ($users as $index => $user)
                @php
                    $canEdit = $currentUser->can('update', $user);
                    $canDelete = $currentUser->can('delete', $user);
                    $initial = strtoupper(mb_substr($user->name, 0, 1));
                    $roleVariant = $user->role === \App\Enums\Role::Admin ? 'violet' : 'info';
                @endphp
                <tr data-user-row data-search="{{ mb_strtolower($user->name . ' ' . $user->email) }}" data-role="{{ $user->role->value }}">
                    <td class="font-mono text-xs w-14">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full border-[2px] border-[#0A0A0A] bg-[#F9C32E] flex items-center justify-center font-mono text-sm font-bold text-[#0A0A0A] shrink-0">
                                {{ $initial }}
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-[#0A0A0A]">{{ $user->name }}</span>
                                @if ($currentUser->id === $user->id)
                                    <x-ui.badge variant="mint">Akun Anda</x-ui.badge>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="font-mono text-xs">{{ $user->email }}</td>
                    <td>
                        <x-ui.badge :variant="$roleVariant">{{ strtoupper($user->role->label()) }}</x-ui.badge>
                    </td>
                    <td class="font-mono text-xs">{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                    <td class="w-48">
                        <div class="flex items-center gap-1.5">
                            @if ($canEdit)
                                <x-ui.button variant="secondary" size="sm" href="{{ route('users.edit', $user) }}">Edit</x-ui.button>
                            @endif

                            @if ($canDelete)
                                <x-ui.button variant="danger" size="sm" data-modal-open="deleteUserModal{{ $user->id }}">Hapus</x-ui.button>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-[#EBE7E7] text-[#4f4633] border-[2px] border-[#0A0A0A] font-mono text-[10px] font-bold uppercase cursor-not-allowed" title="Akun dilindungi">
                                    <span class="material-symbols-outlined text-[14px]">lock</span>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-ui.table>

        <div id="tableEmpty" class="hidden">
            <x-ui.empty-state icon="search_off" title="Pengguna Tidak Ditemukan" description="Tidak ada pengguna yang cocok dengan pencarian atau filter role." />
        </div>

        <x-ui.pagination :paginator="$users" />
    @else
        <div id="tableEmpty">
            <x-ui.empty-state icon="group" title="Belum Ada Pengguna" description="Tambahkan akun pengguna baru ke dalam sistem.">
                <x-ui.button variant="primary" size="sm" icon="person_add" href="{{ route('users.create') }}">
                    Tambah User
                </x-ui.button>
            </x-ui.empty-state>
        </div>
    @endif
</div>
@endsection

@push('modals')
    @foreach ($users as $user)
        @if ($currentUser->can('delete', $user))
            <x-ui.modal id="deleteUserModal{{ $user->id }}" title="Hapus Pengguna">
                <p class="font-sans text-[14px] text-[#0A0A0A] m-0">
                    Apakah Anda yakin ingin menghapus akun <strong>{{ $user->name }}</strong> ({{ $user->email }})?
                </p>
                <form id="deleteUserForm{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
                <x-slot:footer>
                    <x-ui.button variant="secondary" size="sm" data-modal-close="deleteUserModal{{ $user->id }}">Batal</x-ui.button>
                    <x-ui.button type="submit" form="deleteUserForm{{ $user->id }}" variant="danger" size="sm" icon="delete">Hapus User</x-ui.button>
                </x-slot:footer>
            </x-ui.modal>
        @endif
    @endforeach
@endpush

@push('scripts')
<script>
    (function () {
        const searchInput = document.getElementById('tableSearch');
        const roleFilter = document.getElementById('userRoleFilter');
        const rows = document.querySelectorAll('[data-user-row]');
        const counter = document.getElementById('userCounter');
        const tableWrap = document.getElementById('usersTable');
        const emptyBox = document.getElementById('tableEmpty');

        if (!rows.length) return;

        function applyFilter() {
            const q = (searchInput?.value || '').trim().toLowerCase();
            const role = roleFilter?.value || '';
            let visible = 0;

            rows.forEach(function (row) {
                const text = row.getAttribute('data-search') || '';
                const rowRole = row.getAttribute('data-role') || '';
                const match = (!q || text.includes(q)) && (!role || rowRole === role);
                row.classList.toggle('hidden', !match);
                if (match) visible++;
            });

            if (counter) counter.textContent = visible + ' pengguna';
            if (tableWrap && emptyBox) {
                tableWrap.classList.toggle('hidden', visible === 0);
                emptyBox.classList.toggle('hidden', visible > 0);
            }
        }

        searchInput?.addEventListener('input', applyFilter);
        roleFilter?.addEventListener('change', applyFilter);
    })();
</script>
@endpush
