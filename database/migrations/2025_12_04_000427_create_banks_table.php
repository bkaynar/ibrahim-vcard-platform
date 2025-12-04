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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Banka adı
            $table->string('code')->unique(); // Banka kodu (ziraat, halk, vs.)
            $table->string('logo')->nullable(); // Logo dosya yolu
            $table->string('color')->default('#6B7280'); // Banka rengi
            $table->boolean('is_active')->default(true); // Aktif/Pasif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banks');
    }
};
