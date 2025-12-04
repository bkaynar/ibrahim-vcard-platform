<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Shopify Webhook Routes
Route::prefix('webhooks/shopify')->group(function () {
    Route::post('/test', [ShopifyWebhookController::class, 'test'])
        ->name('api.webhooks.shopify.test');
    Route::post('/order-complete', [ShopifyWebhookController::class, 'orderComplete'])
        ->name('api.webhooks.shopify.order-complete');
    Route::post('/{topic}', [ShopifyWebhookController::class, 'handle'])
        ->name('api.webhooks.shopify.handle');
});