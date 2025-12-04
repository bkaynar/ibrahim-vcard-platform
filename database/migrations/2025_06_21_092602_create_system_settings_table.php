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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            
            // Site Ayarları
            $table->string('site_title')->default('VCard Platform');
            $table->text('site_description')->nullable();
            $table->text('site_keywords')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            
            // SMTP Ayarları
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->default(587);
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->enum('smtp_encryption', ['tls', 'ssl', 'none'])->default('tls');
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            $table->boolean('smtp_enabled')->default(false);
            
            // Shopify Entegrasyon Ayarları
            $table->string('shopify_store_url')->nullable();
            $table->string('shopify_access_token')->nullable();
            $table->string('shopify_api_key')->nullable();
            $table->string('shopify_api_secret')->nullable();
            $table->text('shopify_keywords')->nullable(); // JSON formatında
            $table->boolean('shopify_enabled')->default(false);
            
            // Genel Ayarlar
            $table->string('timezone')->default('Europe/Istanbul');
            $table->string('language')->default('tr');
            $table->string('currency')->default('TRY');
            $table->boolean('maintenance_mode')->default(false);
            $table->text('maintenance_message')->nullable();
            
            // SEO Ayarları
            $table->text('google_analytics_id')->nullable();
            $table->text('google_tag_manager_id')->nullable();
            $table->text('facebook_pixel_id')->nullable();
            $table->text('custom_head_code')->nullable();
            $table->text('custom_body_code')->nullable();
            
            // Sosyal Medya Ayarları
            $table->json('social_media_links')->nullable();
            
            // E-ticaret Ayarları
            $table->decimal('tax_rate', 5, 2)->default(0.18); // KDV oranı
            $table->string('payment_currency')->default('TRY');
            $table->json('payment_methods')->nullable(); // Aktif ödeme yöntemleri
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
