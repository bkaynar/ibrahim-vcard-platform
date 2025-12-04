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
        Schema::table('system_settings', function (Blueprint $table) {
            // Shopify alanlarını TEXT yapalım (encrypted değerler için)
            $table->text('shopify_api_secret')->nullable()->change();
            $table->text('shopify_access_token')->nullable()->change();
            $table->string('shopify_store_url', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            // Geri alış için (optional)
            $table->string('shopify_api_secret', 255)->nullable()->change();
            $table->string('shopify_access_token', 255)->nullable()->change();
            $table->string('shopify_store_url', 255)->nullable()->change();
        });
    }
};
