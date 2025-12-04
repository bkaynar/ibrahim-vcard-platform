<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class SystemSettingController extends Controller
{
    /**
     * Display the system settings form.
     */
    public function index()
    {
        $settings = SystemSetting::current();

        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            // Site Ayarları
            'site_title' => 'required|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:1000',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:512',

            // SMTP Ayarları
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|integer|min:1|max:65535',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => ['nullable', Rule::in(['tls', 'ssl', 'none'])],
            'smtp_from_address' => 'nullable|email|max:255',
            'smtp_from_name' => 'nullable|string|max:255',
            'smtp_enabled' => 'boolean',

            // Shopify Ayarları
            'shopify_store_url' => 'nullable|string|max:255',
            'shopify_access_token' => 'nullable|string|max:255',
            'shopify_api_key' => 'nullable|string|max:255',
            'shopify_api_secret' => 'nullable|string|max:255',
            'shopify_keywords' => 'nullable|array',
            'shopify_keywords.*' => 'string|max:100',
            'shopify_enabled' => 'boolean',

            // Genel Ayarlar
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'currency' => 'nullable|string|max:10',
            'maintenance_mode' => 'boolean',
            'maintenance_message' => 'nullable|string|max:1000',

            // SEO Ayarları
            'google_analytics_id' => 'nullable|string|max:50',
            'google_tag_manager_id' => 'nullable|string|max:50',
            'facebook_pixel_id' => 'nullable|string|max:50',
            'custom_head_code' => 'nullable|string|max:5000',
            'custom_body_code' => 'nullable|string|max:5000',

            // Sosyal Medya ve E-ticaret
            'social_media_links' => 'nullable|array',
            'tax_rate' => 'nullable|numeric|min:0|max:1',
            'payment_currency' => 'nullable|string|max:10',
            'payment_methods' => 'nullable|array',
        ]);

        $settings = SystemSetting::current();
        $data = $request->except(['site_logo', 'site_favicon']);

        // Logo dosyası yükleme
        if ($request->hasFile('site_logo')) {
            $logoPath = $request->file('site_logo')->store('logos', 'public');
            $data['site_logo'] = $logoPath;
        }

        // Favicon dosyası yükleme
        if ($request->hasFile('site_favicon')) {
            $faviconPath = $request->file('site_favicon')->store('favicons', 'public');
            $data['site_favicon'] = $faviconPath;
        }

        // Site keywords'ü string formatına çevir
        if (isset($data['site_keywords']) && is_array($data['site_keywords'])) {
            $data['site_keywords'] = implode(',', $data['site_keywords']);
        }

        $settings->update($data);

        return redirect()->back()->with('success', 'Sistem ayarları başarıyla güncellendi!');
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'test_email' => 'required|email',
        ]);

        try {
            // Geçici olarak mail konfigürasyonunu değiştir
            config([
                'mail.mailers.smtp.host' => $request->smtp_host,
                'mail.mailers.smtp.port' => $request->smtp_port,
                'mail.mailers.smtp.username' => $request->smtp_username,
                'mail.mailers.smtp.password' => $request->smtp_password,
                'mail.mailers.smtp.encryption' => $request->smtp_encryption !== 'none' ? $request->smtp_encryption : null,
                'mail.from.address' => $request->smtp_username,
                'mail.from.name' => 'VCard Platform Test',
            ]);

            // Test e-postası gönder
            Mail::raw('Bu bir SMTP test mesajıdır. Ayarlarınız doğru çalışıyor!', function ($message) use ($request) {
                $message->to($request->test_email)
                    ->subject('SMTP Test - VCard Platform');
            });

            return response()->json([
                'success' => true,
                'message' => 'SMTP ayarları başarıyla test edildi! Test e-postası gönderildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'SMTP bağlantı hatası: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Test Shopify connection
     */
    public function testShopify(Request $request)
    {
        $request->validate([
            'shopify_store_url' => 'required|string',
            'shopify_access_token' => 'required|string',
        ]);

        try {
            // Shopify API'yi test et
            $url = "https://{$request->shopify_store_url}/admin/api/2023-10/shop.json";

            $headers = [
                'X-Shopify-Access-Token: ' . $request->shopify_access_token,
                'Content-Type: application/json',
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $data = json_decode($response, true);
                return response()->json([
                    'success' => true,
                    'message' => 'Shopify bağlantısı başarılı!',
                    'shop_name' => $data['shop']['name'] ?? 'Bilinmeyen',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Shopify bağlantı hatası. Lütfen ayarlarınızı kontrol ediniz.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Shopify API hatası: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            return redirect()->back()->with('success', 'Önbellek başarıyla temizlendi!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Önbellek temizleme hatası: ' . $e->getMessage());
        }
    }
}
