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
        Schema::create('bank_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bank_id')->constrained()->cascadeOnDelete();
            $table->string('iban')->nullable(); // IBAN numarası
            $table->string('account_holder')->nullable(); // Hesap sahibi
            $table->string('branch')->nullable(); // Şube
            $table->boolean('is_primary')->default(false); // Ana hesap mı?
            $table->timestamps();

            // Bir kullanıcı aynı bankada birden fazla hesap açabilir, bu yüzden unique constraint koymuyoruz
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_user');
    }
};
