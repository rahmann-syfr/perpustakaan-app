<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Menambahkan kolom cover_image, boleh kosong (nullable)
            $table->string('cover_image')->nullable()->after('available_stock');
        });
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Menghapus kolom jika di-rollback
            $table->dropColumn('cover_image');
        });
    }
};