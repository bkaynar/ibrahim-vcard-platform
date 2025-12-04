<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemSetting::updateOrCreate(
            ['id' => 1],
            [
                // Site Ayarları
                'site_title' => 'VCard Platform',
                'site_description' => 'Modern dijital kartvizit platformu. Kişisel ve profesyonel bilgilerinizi dijital ortamda paylaşın, networkinginizi güçlendirin.',
                'site_keywords' => 'dijital kartvizit, vcard, networking, kartvizit, dijital, platform, profesyonel, iş ağı, contact',
                
                // SMTP Ayarları (Örnek - Gmail)
                'smtp_host' => 'smtp.gmail.com',
                'smtp_port' => 587,
                'smtp_username' => 'your-email@gmail.com',
                'smtp_password' => 'your-app-password', // Bu şifrelenecek
                'smtp_encryption' => 'tls',
                'smtp_from_address' => 'noreply@vcardplatform.com',
                'smtp_from_name' => 'VCard Platform',
                'smtp_enabled' => false, // Başlangıçta kapalı
                
                // Shopify Ayarları
                'shopify_store_url' => 'your-store.myshopify.com',
                'shopify_access_token' => '', // Bu şifrelenecek
                'shopify_api_key' => 'your-api-key',
                'shopify_api_secret' => 'your-api-secret', // Bu şifrelenecek
                'shopify_keywords' => [
                    'dijital kartvizit',
                    'vcard',
                    'networking',
                    'profesyonel kartvizit',
                    'qr kod kartvizit',
                    'akıllı kartvizit',
                    'online kartvizit',
                    'mobil kartvizit'
                ],
                'shopify_enabled' => false,
                
                // Genel Ayarlar
                'timezone' => 'Europe/Istanbul',
                'language' => 'tr',
                'currency' => 'TRY',
                'maintenance_mode' => false,
                'maintenance_message' => 'Sistem bakımda. Lütfen daha sonra tekrar deneyiniz.',
                
                // SEO Ayarları
                'google_analytics_id' => 'G-XXXXXXXXXX',
                'google_tag_manager_id' => 'GTM-XXXXXXX',
                'facebook_pixel_id' => '',
                'custom_head_code' => '<!-- Custom head code buraya -->',
                'custom_body_code' => '<!-- Custom body code buraya -->',
                
                // Sosyal Medya Bağlantıları
                'social_media_links' => [
                    'facebook' => 'https://facebook.com/vcardplatform',
                    'twitter' => 'https://twitter.com/vcardplatform',
                    'instagram' => 'https://instagram.com/vcardplatform',
                    'linkedin' => 'https://linkedin.com/company/vcardplatform',
                    'youtube' => 'https://youtube.com/@vcardplatform',
                    'tiktok' => 'https://tiktok.com/@vcardplatform',
                ],
                
                // E-ticaret Ayarları
                'tax_rate' => 0.20, // %20 KDV
                'payment_currency' => 'TRY',
                'payment_methods' => [
                    'credit_card' => true,
                    'paypal' => false,
                    'bank_transfer' => true,
                    'crypto' => false,
                    'apple_pay' => true,
                    'google_pay' => true,
                ],
            ]
        );
        
        $this->command->info('✅ Sistem ayarları başarıyla oluşturuldu!');
        $this->command->info('🌐 Site Title: VCard Platform');
        $this->command->info('📧 SMTP: Yapılandırma gerekli');
        $this->command->info('🛒 Shopify: Yapılandırma gerekli');
        $this->command->info('⚙️  Genel ayarlar: Varsayılan değerlerle ayarlandı');
    }
}
