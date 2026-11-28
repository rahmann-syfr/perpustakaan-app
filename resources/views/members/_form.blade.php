@php
    $isEdit = isset($member) && $member !== null;
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <x-ui.input
            name="name"
            label="Nama Lengkap"
            :value="$isEdit ? $member->name : null"
            placeholder="Masukkan nama lengkap"
            :required="true"
        />
    </div>

    <x-ui.input
        name="email"
        type="email"
        label="Email"
        :value="$isEdit ? $member->email : null"
        placeholder="nama@email.com"
        :required="true"
    />

    <x-ui.input
        name="phone"
        label="No HP"
        :value="$isEdit ? $member->phone : null"
        placeholder="081234567890"
        :required="true"
    />

    <div class="md:col-span-2">
        <x-ui.input
            name="address"
            type="textarea"
            label="Alamat"
            :value="$isEdit ? $member->address : null"
            placeholder="Alamat lengkap anggota"
            maxlength="500"
            :required="true"
        />
    </div>
</div>
