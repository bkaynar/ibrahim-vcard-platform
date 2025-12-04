<?php

namespace App\Services;

use App\Models\User;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ShopifyWebhookService
{
    /**
     * Shopify'dan gelen sipariş tamamlandığında kullanıcı oluştur
     */
    public function handleOrderComplete($orderData)
    {
        // Sipariş bilgilerinden email ve isim al
        $email = $orderData['email'] ?? null;
        $name = $this->extractCustomerName($orderData);

        if (!$email) {
            throw new \Exception('Email adresi bulunamadı.');
        }

        // Kullanıcı zaten varsa işlem yapma
        if (User::where('email', $email)->exists()) {
            return [
                'success' => false,
                'user' => User::where('email', $email)->first()
            ];
        }

        // Shopify ayarlarını kontrol et
        $settings = SystemSetting::current();
        if (!$settings->shopify_enabled) {
            throw new \Exception('Shopify entegrasyonu devre dışı.');
        }

        // Siparişteki ürünlerin keywords ile eşleşip eşleşmediğini kontrol et
        if (!$this->hasMatchingProducts($orderData, $settings->shopify_keywords ?? [])) {
            return [
                'success' => false,
                'message' => 'Sipariş geçerli ürünleri içermiyor.'
            ];
        }

        // Benzersiz kullanıcı adı oluştur
        $username = $this->generateUniqueUsername($name, $email);

        // Geçici şifre oluştur
        $tempPassword = Str::random(12);

        // Kullanıcı oluştur
        $user = User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($tempPassword),
            'email_verified_at' => now(), // Shopify'dan gelen kullanıcılar otomatik doğrulanmış sayılır
        ]);

        // User rolü ata
        $user->assignRole('user');

        // Şifre sıfırlama maili gönder
        $this->sendPasswordResetEmail($user);

        return [
            'success' => true,
            'message' => 'Kullanıcı başarıyla oluşturuldu ve şifre sıfırlama maili gönderildi.',
            'user' => $user
        ];
    }

    /**
     * Müşteri ismini sipariş verisinden çıkar
     */
    private function extractCustomerName($orderData): string
    {
        $firstName = $orderData['customer']['first_name'] ?? '';
        $lastName = $orderData['customer']['last_name'] ?? '';

        $name = trim($firstName . ' ' . $lastName);

        // Eğer isim yoksa email'in @ öncesi kısmını kullan
        if (empty($name)) {
            $name = explode('@', $orderData['email'] ?? 'user')[0];
        }

        return $name ?: 'Kullanıcı';
    }

    /**
     * Siparişteki ürünlerin belirtilen keywords ile eşleşip eşleşmediğini kontrol et
     */
    private function hasMatchingProducts($orderData, array $keywords): bool
    {
        if (empty($keywords)) {
            return true; // Keywords yoksa tüm siparişleri kabul et
        }

        $lineItems = $orderData['line_items'] ?? [];

        foreach ($lineItems as $item) {
            $productTitle = strtolower($item['title'] ?? '');
            $productName = strtolower($item['name'] ?? '');
            $sku = strtolower($item['sku'] ?? '');

            // Gerçek Shopify webhook'unda product.tags yok, sadület title/name/sku'dan arama yapıyoruz
            $searchText = $productTitle . ' ' . $productName . ' ' . $sku;

            foreach ($keywords as $keyword) {
                if (strpos($searchText, strtolower(trim($keyword))) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Benzersiz kullanıcı adı oluştur
     */
    private function generateUniqueUsername($name, $email): string
    {
        // İsimden slug oluştur
        $baseUsername = Str::slug(Str::lower($name));

        // Eğer isim uygun değilse email'den oluştur
        if (empty($baseUsername) || strlen($baseUsername) < 3) {
            $baseUsername = explode('@', $email)[0];
            $baseUsername = Str::slug(Str::lower($baseUsername));
        }

        // Benzersiz hale getir
        $username = $baseUsername;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    /**
     * Kullanıcıya şifre sıfırlama maili gönder
     */
    private function sendPasswordResetEmail(User $user): void
    {
        Password::sendResetLink(['email' => $user->email]);
    }

    /**
     * Webhook verilerini doğrula
     */
    public function verifyWebhook($data, $hmacHeader): bool
    {
        $settings = SystemSetting::current();
        $webhookSecret = $settings->shopify_api_secret;

        if (!$webhookSecret || !$hmacHeader) {
            return false;
        }

        $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $webhookSecret, true));

        return hash_equals($calculated_hmac, $hmacHeader);
    }
}
