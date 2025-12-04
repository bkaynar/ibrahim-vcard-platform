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
        Schema::table('users', function (Blueprint $table) {
            // E-posta nullable yap
            $table->string('email')->nullable()->change();

            // Telefon nullable ve unique yap
            $table->string('phone')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // E-posta unique yap ve nullable kaldır
            $table->string('email')->nullable(false)->unique()->change();

            // Telefon unique constraint'i kaldır
            $table->string('phone')->unique(false)->change();
        });
    }
};
