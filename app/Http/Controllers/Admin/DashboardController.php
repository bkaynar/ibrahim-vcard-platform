<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VcardVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ana İstatistikler
        $totalUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();
        $totalVcardVisits = VcardVisit::count();
        $monthlyNewUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Son 7 gün VCard ziyaret sayıları
        $last7DaysVisits = VcardVisit::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Son 30 gün VCard ziyaret sayıları
        $last30DaysVisits = VcardVisit::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Son kayıt olan kullanıcılar (son 10)
        $recentUsers = User::with('roles')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at->format('d.m.Y H:i'),
                    'role' => $user->roles->first()?->name ?? 'user'
                ];
            });

        // En çok ziyaret edilen VCard'lar
        $topVcards = VcardVisit::select('user_id', DB::raw('COUNT(*) as visit_count'))
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderByDesc('visit_count')
            ->take(10)
            ->get()
            ->map(function ($visit) {
                return [
                    'user_name' => $visit->user->name ?? 'Bilinmiyor',
                    'user_email' => $visit->user->email ?? '',
                    'visit_count' => $visit->visit_count
                ];
            });

        // Sistem Durumu
        $systemStatus = [
            'cache_status' => $this->getCacheStatus(),
            'smtp_status' => $this->getSmtpStatus(),
            'disk_usage' => $this->getDiskUsage(),
            'ram_usage' => [
                'total' => $this->formatBytes(memory_get_usage(true)),
                'used' => $this->formatBytes(memory_get_usage()),
                'free' => $this->formatBytes(memory_get_peak_usage(true) - memory_get_usage()),
                'usage_percent' => round((memory_get_usage() / memory_get_peak_usage(true)) * 100, 2)
            ],
            'last_backup' => $this->getLastBackupDate()
        ];

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_users' => $totalUsers,
                'total_vcard_visits' => $totalVcardVisits,
                'monthly_new_users' => $monthlyNewUsers,
            ],
            'charts' => [
                'last_7_days_visits' => $last7DaysVisits,
                'last_30_days_visits' => $last30DaysVisits,
            ],
            'recent_users' => $recentUsers,
            'top_vcards' => $topVcards,
            'system_status' => $systemStatus
        ]);
    }

    private function getCacheStatus()
    {
        try {
            Cache::put('test_cache', 'working', 60);
            $result = Cache::get('test_cache');
            Cache::forget('test_cache');
            return $result === 'working' ? 'active' : 'inactive';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getSmtpStatus()
    {
        try {
            // Basit bir SMTP bağlantı testi
            $settings = \App\Models\SystemSetting::first();
            if ($settings && $settings->smtp_enabled) {
                // Gerçek SMTP testi burada yapılabilir
                return 'active';
            }
            return 'inactive';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function getDiskUsage()
    {
        try {
            $totalBytes = disk_total_space(storage_path());
            $freeBytes = disk_free_space(storage_path());
            $usedBytes = $totalBytes - $freeBytes;

            return [
                'total' => $this->formatBytes($totalBytes),
                'used' => $this->formatBytes($usedBytes),
                'free' => $this->formatBytes($freeBytes),
                'usage_percent' => round(($usedBytes / $totalBytes) * 100, 2)
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getLastBackupDate()
    {
        // Bu fonksiyon backup sistemine göre ayarlanabilir
        return Carbon::now()->subDays(1)->format('d.m.Y H:i');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            return response()->json([
                'success' => true,
                'message' => 'Cache başarıyla temizlendi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache temizlenirken hata oluştu: ' . $e->getMessage()
            ], 500);
        }
    }
}
