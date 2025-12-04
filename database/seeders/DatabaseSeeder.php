<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Önce temel seeder'ları çalıştır
        $this->call([
            UserSeeder::class,
            SystemSettingSeeder::class, // Sistem ayarları seeder'ı
            VcardVisitSeeder::class, // VCard ziyaret test verileri
        ]);

        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
