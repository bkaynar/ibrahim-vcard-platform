<?php

namespace App\Http\Controllers;

use App\Services\ShopifyWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ShopifyWebhookController extends Controller
{
    protected $webhookService;

    public function __construct(ShopifyWebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    /**
     * Shopify sipariş tamamlandığında çağrılacak webhook
     */
    public function orderComplete(Request $request): Response
    {
        try {
            Log::info('Shopify webhook alındı - RAW', [
                'headers' => $request->headers->all(),
                'content' => $request->getContent(),
                'method' => $request->getMethod(),
                'url' => $request->fullUrl()
            ]);

            // JSON'u dosyaya kaydet (debug için)
            $filename = storage_path('logs/webhook_' . date('Y-m-d_H-i-s') . '_' . uniqid() . '.json');
            file_put_contents($filename, $request->getContent());
            Log::info('Webhook JSON dosyaya kaydedildi: ' . $filename);

            // Webhook doğrulaması
            $hmacHeader = $request->header('X-Shopify-Hmac-Sha256');
            $data = $request->getContent();

            Log::info('Webhook doğrulama bilgileri', [
                'hmac_header' => $hmacHeader,
                'data_length' => strlen($data)
            ]);

            // TEST İÇİN GEÇİCİ OLARAK BYPASS EDİLDİ
            if (!$this->webhookService->verifyWebhook($data, $hmacHeader)) {
                Log::warning('Shopify webhook doğrulaması başarısız - TEST İÇİN DEVAM EDİLİYOR', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'hmac_header' => $hmacHeader
                ]);

                // return response('Unauthorized', 401); // TEST İÇİN YORUMLANDİ
            }

            // JSON verisini decode et
            $orderData = json_decode($data, true);

            if (!$orderData) {
                Log::error('Shopify webhook JSON decode hatası', ['data' => $data]);
                return response('Bad Request', 400);
            }

            Log::info('Shopify sipariş webhook alındı', [
                'order_id' => $orderData['id'] ?? 'unknown',
                'email' => $orderData['email'] ?? 'unknown'
            ]);

            // Kullanıcı oluşturma işlemini gerçekleştir
            $result = $this->webhookService->handleOrderComplete($orderData);

            if ($result['success']) {
                Log::info('Shopify webhook kullanıcı oluşturma başarılı', [
                    'user_id' => $result['user']->id,
                    'email' => $result['user']->email
                ]);
            } else {
                Log::info('Shopify webhook kullanıcı oluşturma atlandı', [
                    'reason' => $result['message'] ?? 'Bilinmeyen sebep'
                ]);
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Shopify webhook hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->getContent()
            ]);

            return response('Internal Server Error', 500);
        }
    }

    /**
     * Test endpoint - webhook'un çalışıp çalışmadığını test etmek için
     */
    public function test(Request $request)
    {
        Log::info('Shopify webhook test endpoint çağrıldı', [
            'headers' => $request->headers->all(),
            'content' => $request->getContent(),
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test webhook başarılı',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Diğer Shopify webhook eventleri için genel handler
     */
    public function handle(Request $request): Response
    {
        try {
            // Webhook doğrulaması
            $hmacHeader = $request->header('X-Shopify-Hmac-Sha256');
            $data = $request->getContent();

            if (!$this->webhookService->verifyWebhook($data, $hmacHeader)) {
                return response('Unauthorized', 401);
            }

            $topic = $request->header('X-Shopify-Topic');

            Log::info('Shopify webhook alındı', [
                'topic' => $topic,
                'shop' => $request->header('X-Shopify-Shop-Domain')
            ]);

            // Şu an için sadece log kaydet, gelecekte diğer eventler için işlem eklenebilir

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Shopify webhook genel hatası', [
                'error' => $e->getMessage(),
                'topic' => $request->header('X-Shopify-Topic')
            ]);

            return response('Internal Server Error', 500);
        }
    }
}