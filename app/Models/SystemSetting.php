<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class SystemSetting extends Model
{
    protected $fillable = [
        // Site Ayarları
        'site_title',
        'site_description',
        'site_keywords',
        'site_logo',
        'site_favicon',
        
        // SMTP Ayarları
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'smtp_enabled',
        
        // Shopify Ayarları
        'shopify_store_url',
        'shopify_access_token',
        'shopify_api_key',
        'shopify_api_secret',
        'shopify_keywords',
        'shopify_enabled',
        
        // Genel Ayarlar
        'timezone',
        'language',
        'currency',
        'maintenance_mode',
        'maintenance_message',
        
        // SEO Ayarları
        'google_analytics_id',
        'google_tag_manager_id',
        'facebook_pixel_id',
        'custom_head_code',
        'custom_body_code',
        
        // Sosyal Medya ve E-ticaret
        'social_media_links',
        'tax_rate',
        'payment_currency',
        'payment_methods',
    ];

    protected $casts = [
        'smtp_enabled' => 'boolean',
        'shopify_enabled' => 'boolean',
        'maintenance_mode' => 'boolean',
        'social_media_links' => 'array',
        'payment_methods' => 'array',
        'shopify_keywords' => 'array',
        'tax_rate' => 'decimal:2',
    ];

    /**
     * Singleton pattern - Sadece bir ayar kaydı olacak
     */
    public static function current()
    {
        return static::firstOrCreate(
            ['id' => 1],
            [
                'site_title' => 'VCard Platform',
                'site_description' => 'Dijital kartvizit platformu',
                'timezone' => 'Europe/Istanbul',
                'language' => 'tr',
                'currency' => 'TRY',
                'payment_currency' => 'TRY',
                'tax_rate' => 0.18,
            ]
        );
    }

    /**
     * SMTP şifresi için güvenli erişim
     */
    protected function smtpPassword(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? decrypt($value) : null,
            set: fn (?string $value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Shopify API secret için güvenli erişim
     */
    protected function shopifyApiSecret(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? decrypt($value) : null,
            set: fn (?string $value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Shopify access token için güvenli erişim
     */
    protected function shopifyAccessToken(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? decrypt($value) : null,
            set: fn (?string $value) => $value ? encrypt($value) : null,
        );
    }

    /**
     * Site keywords'ü array'e çevir
     */
    public function getSiteKeywordsArrayAttribute()
    {
        return $this->site_keywords ? explode(',', $this->site_keywords) : [];
    }

    /**
     * Site keywords'ü string'e çevir
     */
    public function setSiteKeywordsArrayAttribute($value)
    {
        $this->attributes['site_keywords'] = is_array($value) ? implode(',', $value) : $value;
    }

    /**
     * SMTP ayarlarının geçerli olup olmadığını kontrol et
     */
    public function isSmtpConfigured(): bool
    {
        return $this->smtp_enabled && 
               !empty($this->smtp_host) && 
               !empty($this->smtp_username) && 
               !empty($this->smtp_password);
    }

    /**
     * Shopify ayarlarının geçerli olup olmadığını kontrol et
     */
    public function isShopifyConfigured(): bool
    {
        return $this->shopify_enabled && 
               !empty($this->shopify_store_url) && 
               !empty($this->shopify_access_token);
    }

    /**
     * Bakım modu durumunu kontrol et
     */
    public function isMaintenanceMode(): bool
    {
        return $this->maintenance_mode;
    }

    /**
     * Varsayılan sosyal medya platformları
     */
    public function getDefaultSocialMediaLinks(): array
    {
        return [
            'facebook' => '',
            'twitter' => '',
            'instagram' => '',
            'linkedin' => '',
            'youtube' => '',
            'tiktok' => '',
        ];
    }

    /**
     * Varsayılan ödeme yöntemleri
     */
    public function getDefaultPaymentMethods(): array
    {
        return [
            'credit_card' => true,
            'paypal' => false,
            'bank_transfer' => true,
            'crypto' => false,
        ];
    }
}
