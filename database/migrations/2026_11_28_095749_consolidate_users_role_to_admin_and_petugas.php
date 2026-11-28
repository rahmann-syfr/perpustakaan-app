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
        $remainingAnggota = DB::table('users')
            ->where('role', 'anggota')
            ->count();

        if ($remainingAnggota > 0) {
            throw new RuntimeException("Migrasi dihentikan: masih terdapat {$remainingAnggota} user ber-role 'anggota' pada tabel users.");
        }

        DB::table('users')
            ->where('role', 'superadmin')
            ->update(['role' => 'admin']);

        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` ENUM('admin', 'petugas') NOT NULL DEFAULT 'petugas'");

            return;
        }

        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->dropIndex(['role']);
                $table->dropColumn('role');
            });
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['admin', 'petugas'])->default('petugas')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE `users` MODIFY COLUMN `role` VARCHAR(255) NOT NULL DEFAULT 'petugas'");

            return;
        }

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role')->default('petugas')->index();
        });
    }
};
