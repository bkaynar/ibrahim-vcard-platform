<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Inertia shared data
        Inertia::share([
            'site' => function () {
                $settings = SystemSetting::current();

                return [
                    'title' => $settings->site_title ?? 'VCard Platform',
                    'description' => $settings->site_description ?? 'Dijital kartvizit platformu',
                    'keywords' => $settings->site_keywords ?? 'vcard, dijital kartvizit, qr kod',
                    'language' => $settings->language ?? 'tr',
                ];
            },

            'flash' => function (Request $request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                ];
            }
        ]);
    }
}
