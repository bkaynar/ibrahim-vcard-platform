<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BankController;
use App\Http\Controllers\Admin\SystemSettingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\VCardController;
use App\Http\Controllers\WelcomeController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard')
        ->middleware('role:admin');

    // Ana dashboard - role'a göre yönlendirme
    Route::get('/dashboard', function () {
        /** @var User $user */
        $user = Auth::user();
        if ($user && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return app(UserDashboardController::class)->index();
    })->name('dashboard');

    // User Dashboard (direkt erişim)
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard')
        ->middleware('role:user');

    // User Profile Routes
    Route::middleware('auth')->group(function () {
        Route::get('/user/profile/edit', [UserProfileController::class, 'edit'])->name('user.profile.edit');
        Route::post('/user/profile', [UserProfileController::class, 'update'])->name('user.profile.update');
        Route::delete('/user/profile/photo', [UserProfileController::class, 'destroyProfilePhoto'])->name('user.profile.photo.destroy');
        Route::delete('/user/profile/cover', [UserProfileController::class, 'destroyCoverPhoto'])->name('user.profile.cover.destroy');
        Route::get('/user/statistics', [UserDashboardController::class, 'statistics'])->name('user.statistics');
    });

    Route::resource('/admin/users', UserController::class)->names('admin.users');
    Route::resource('/admin/banks', BankController::class)->names('admin.banks')->middleware('role:admin');

    // Excel Import Routes
    Route::get('/admin/users-import', [UserController::class, 'importForm'])->name('admin.users.import.form');
    Route::post('/admin/users-import', [UserController::class, 'import'])->name('admin.users.import');
    Route::get('/admin/users-import/template', [UserController::class, 'downloadTemplate'])->name('admin.users.import.template');

    // Admin Dashboard ve Eylemler
    Route::middleware('role:admin')->group(function () {
        Route::post('/admin/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('admin.dashboard.clear-cache');
    });

    // Sistem Ayarları Route'ları
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/settings', [SystemSettingController::class, 'index'])->name('admin.settings.index');
        Route::post('/admin/settings', [SystemSettingController::class, 'update'])->name('admin.settings.update');
        Route::post('/admin/settings/test-smtp', [SystemSettingController::class, 'testSmtp'])->name('admin.settings.test-smtp');
        Route::post('/admin/settings/test-shopify', [SystemSettingController::class, 'testShopify'])->name('admin.settings.test-shopify');
        Route::post('/admin/settings/clear-cache', [SystemSettingController::class, 'clearCache'])->name('admin.settings.clear-cache');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

// VCard Public Route (En sonda, çakışma olmaması için)
Route::get('/{registration_number}', [\App\Http\Controllers\VCardController::class, 'show'])->name('vcard.show');
