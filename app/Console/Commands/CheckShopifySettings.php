<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;

class CheckShopifySettings extends Command
{
    protected $signature = 'shopify:check-settings';
    protected $description = 'Shopify ayarlarını kontrol et';

    public function handle()
    {
        $settings = SystemSetting::current();

        $this->info('=== SHOPIFY AYARLARI ===');
        $this->line('Shopify Enabled: ' . ($settings->shopify_enabled ? 'EVET' : 'HAYIR'));
        $this->line('Store URL: ' . ($settings->shopify_store_url ?: 'YOK'));
        $this->line('API Key: ' . ($settings->shopify_api_key ? 'VAR' : 'YOK'));
        $this->line('API Secret: ' . ($settings->shopify_api_secret ? 'VAR' : 'YOK'));
        $this->line('Access Token: ' . ($settings->shopify_access_token ? 'VAR' : 'YOK'));

        if ($settings->shopify_keywords) {
            $this->line('Keywords: ' . implode(', ', $settings->shopify_keywords));
        } else {
            $this->line('Keywords: YOK (tüm ürünler kabul edilir)');
        }

        $this->line('');

        if ($settings->isShopifyConfigured()) {
            $this->info('✅ Shopify ayarları tamamlanmış');
        } else {
            $this->error('❌ Shopify ayarları eksik veya devre dışı');
        }

        return 0;
    }
}