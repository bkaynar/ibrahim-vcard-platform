<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Eski büyük harfli admin rolünü sil
        \Spatie\Permission\Models\Role::where('name', 'Admin')->delete();

        // Roller yoksa oluştur
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole  = Role::firstOrCreate(['name' => 'user']);

        // Admin kullanıcı
        $admin = User::firstOrCreate(
            ['email' => 'admin@dijivizital.com'],
            [
                'name' => 'Admin Kullanıcı',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        // Tüm rollerini kaldırıp sadece admin rolünü ata
        $admin->syncRoles([$adminRole]);

        // Test kullanıcısı - Sicil No: 1
        $testUser = User::updateOrCreate(
            ['username' => 'ahmet-yilmaz'],
            [
                'name' => 'Ahmet Yılmaz',
                'email' => 'ahmet.yilmaz@dijivizital.com',
                'phone' => '+90 532 123 4567',
                'registration_number' => '1',
                'password' => Hash::make('1'),
                'address' => 'Atatürk Mahallesi, Cumhuriyet Caddesi No: 123/5, Kadıköy/İstanbul',
                'bio' => 'Yazılım geliştirme alanında 10 yıllık deneyime sahip Senior Full Stack Developer. React, Vue.js, Laravel ve Node.js teknolojilerinde uzmanım.',
                'socials' => [
                    'twitter' => 'ahmetyilmaz',
                    'linkedin' => 'ahmet-yilmaz',
                    'github' => 'ahmetyilmaz',
                    'instagram' => 'ahmet.yilmaz',
                    'facebook' => 'ahmet.yilmaz',
                    'youtube' => 'ahmetyilmaz',
                    'website' => 'https://ahmetyilmaz.dev',
                ],
                'company_info' => [
                    'company_name' => 'DijiVizital Teknoloji A.Ş.',
                    'job_title' => 'Senior Full Stack Developer',
                    'company_address' => 'Maslak Mahallesi, Büyükdere Caddesi No: 255, Sarıyer/İstanbul',
                    'company_phone' => '+90 212 123 4567',
                    'company_email' => 'info@dijivizital.com',
                    'company_website' => 'https://dijivizital.com',
                    'tax_office' => 'Sarıyer Vergi Dairesi',
                    'tax_number' => '1234567890',
                ],
                'bank_info' => [
                    'bank_name' => 'Ziraat Bankası',
                    'iban' => 'TR33 0001 0012 3456 7890 1234 56',
                    'bank_branch' => 'Maslak Şubesi',
                    'bank_account_holder' => 'Ahmet Yılmaz',
                ],
                'email_verified_at' => now(),
            ]
        );
        $testUser->syncRoles([$userRole]);
    }
}
