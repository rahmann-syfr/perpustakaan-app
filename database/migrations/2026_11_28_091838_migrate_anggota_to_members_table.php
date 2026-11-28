<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Validasi: pastikan tidak ada borrowings atau fine_payments yang menunjuk ke user NON-anggota.
        $invalidBorrowings = DB::table('borrowings')
            ->leftJoin('users', 'borrowings.user_id', '=', 'users.id')
            ->where(function ($query): void {
                $query->whereNull('users.id')
                    ->orWhere('users.role', '!=', 'anggota');
            })
            ->select(['borrowings.id', 'borrowings.user_id', 'users.role', 'users.email'])
            ->get();

        if ($invalidBorrowings->isNotEmpty()) {
            $details = $invalidBorrowings
                ->map(fn ($row) => "borrowing #{$row->id} (user_id={$row->user_id}, role=".($row->role ?? 'NULL').')')
                ->implode(', ');

            throw new RuntimeException('Migrasi dihentikan: ditemukan data borrowings yang menunjuk ke user non-anggota: '.$details);
        }

        $invalidFinePayments = DB::table('fine_payments')
            ->leftJoin('users', 'fine_payments.user_id', '=', 'users.id')
            ->where(function ($query): void {
                $query->whereNull('users.id')
                    ->orWhere('users.role', '!=', 'anggota');
            })
            ->select(['fine_payments.id', 'fine_payments.user_id', 'users.role', 'users.email'])
            ->get();

        if ($invalidFinePayments->isNotEmpty()) {
            $details = $invalidFinePayments
                ->map(fn ($row) => "fine_payment #{$row->id} (user_id={$row->user_id}, role=".($row->role ?? 'NULL').')')
                ->implode(', ');

            throw new RuntimeException('Migrasi dihentikan: ditemukan data fine_payments yang menunjuk ke user non-anggota: '.$details);
        }

        // 2. Salin semua users ber-role 'anggota' ke tabel members.
        $anggotaUsers = DB::table('users')
            ->where('role', 'anggota')
            ->orderBy('id')
            ->get(['id', 'name', 'email', 'phone', 'address', 'created_at', 'updated_at']);

        foreach ($anggotaUsers as $user) {
            DB::table('members')->insert([
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);
        }

        // Pemetaan user_id lama -> member_id baru berdasarkan email.
        $membersByEmail = DB::table('members')->pluck('id', 'email');
        $userIdToMemberId = [];

        foreach ($anggotaUsers as $user) {
            if (isset($membersByEmail[$user->email])) {
                $userIdToMemberId[$user->id] = $membersByEmail[$user->email];
            }
        }

        // 3. Tambah borrowings.member_id (nullable dulu), isi dari pemetaan, jadikan NOT NULL + FK, lalu drop user_id.
        Schema::table('borrowings', function (Blueprint $table): void {
            $table->unsignedBigInteger('member_id')->nullable()->after('id');
        });

        foreach ($userIdToMemberId as $userId => $memberId) {
            DB::table('borrowings')
                ->where('user_id', $userId)
                ->update(['member_id' => $memberId]);
        }

        if (DB::table('borrowings')->whereNull('member_id')->exists()) {
            throw new RuntimeException('Migrasi dihentikan: terdapat baris borrowings dengan member_id NULL setelah pemetaan.');
        }

        Schema::table('borrowings', function (Blueprint $table): void {
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->foreign('member_id')->references('id')->on('members')->restrictOnDelete();
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        // 4. Tambah fine_payments.member_id (nullable dulu), isi dari pemetaan, jadikan NOT NULL + FK, lalu drop user_id.
        Schema::table('fine_payments', function (Blueprint $table): void {
            $table->unsignedBigInteger('member_id')->nullable()->after('borrowing_id');
        });

        foreach ($userIdToMemberId as $userId => $memberId) {
            DB::table('fine_payments')
                ->where('user_id', $userId)
                ->update(['member_id' => $memberId]);
        }

        if (DB::table('fine_payments')->whereNull('member_id')->exists()) {
            throw new RuntimeException('Migrasi dihentikan: terdapat baris fine_payments dengan member_id NULL setelah pemetaan.');
        }

        Schema::table('fine_payments', function (Blueprint $table): void {
            $table->unsignedBigInteger('member_id')->nullable(false)->change();
            $table->foreign('member_id')->references('id')->on('members')->restrictOnDelete();
            $table->index(['member_id', 'created_at']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropColumn('user_id');
        });

        // 5. Hapus users ber-role 'anggota' setelah pemetaan berhasil.
        DB::table('users')->where('role', 'anggota')->delete();
    }

    /**
     * Reverse the migrations.
     *
     * Catatan: Pemulihan data penuh tidak dimungkinkan karena akun user ber-role
     * 'anggota' (beserta hash password dan token login-nya) telah dihapus dari
     * tabel users pada saat migrasi up(). Rollback ini hanya mengembalikan skema
     * kolom user_id (nullable) pada borrowings dan fine_payments.
     */
    public function down(): void
    {
        Schema::table('fine_payments', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->after('borrowing_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index(['user_id', 'created_at']);
            $table->dropForeign(['member_id']);
            $table->dropIndex(['member_id', 'created_at']);
            $table->dropColumn('member_id');
        });

        Schema::table('borrowings', function (Blueprint $table): void {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dropForeign(['member_id']);
            $table->dropColumn('member_id');
        });
    }
};
