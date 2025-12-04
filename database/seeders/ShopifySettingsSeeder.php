<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;
use Spatie\Permission\Models\Role;

class ShopifySettingsSeeder extends Seeder
{
    public function run(): void
    {
        // SystemSetting tablosunu kontrol et
        $settings = SystemSetting::first();

        if (!$settings) {
            $settings = new SystemSetting();
        }

        // Shopify ayarlarını etkinleştir
        $settings->shopify_enabled = true;
        $settings->shopify_store_url = 'era8sc-bc.myshopify.com';
        $settings->shopify_api_secret = 'be6bd573d2f3a94a3b79e62bef8173aa4861b15065dd9fcf908fa2216799c1bc'; // Shopify webhook secret
        $settings->shopify_access_token = 'dummy-token'; // Şimdilik dummy
        $settings->save();

        // Gerekli rolleri oluştur
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

        echo "✅ Shopify ayarları etkinleştirildi\n";
        echo "✅ Roller oluşturuldu\n";
        echo "⚠️  UYARI: shopify_api_secret'ı gerçek değerle değiştirmeyi unutma!\n";
    }
}
