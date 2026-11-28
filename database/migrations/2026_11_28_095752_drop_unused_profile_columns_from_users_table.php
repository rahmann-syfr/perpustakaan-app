<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'address', 'institution', 'birth_date', 'gender']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 30)->nullable()->after('email');
            $table->string('address', 255)->nullable()->after('phone');
            $table->string('institution', 120)->nullable()->after('address');
            $table->date('birth_date')->nullable()->after('institution');
            $table->enum('gender', ['male', 'female'])->nullable()->after('birth_date');
        });
    }
};
