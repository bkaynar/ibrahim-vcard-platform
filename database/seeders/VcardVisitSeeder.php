<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VcardVisit;
use Carbon\Carbon;

class VcardVisitSeeder extends Seeder
{
    public function run(): void
    {
        // Kullanıcıları al
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $visits = [];

        // Son 30 gün için test verileri oluştur
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);

            // Her gün için random ziyaret sayısı (0-50 arası)
            $visitsCount = rand(0, 50);

            for ($j = 0; $j < $visitsCount; $j++) {
                $visits[] = [
                    'user_id' => $users->random()->id,
                    'ip_address' => $this->generateRandomIp(),
                    'user_agent' => $this->getRandomUserAgent(),
                    'visited_at' => $date->copy()->addMinutes(rand(0, 1439)), // Random zaman
                    'created_at' => $date->copy()->addMinutes(rand(0, 1439)),
                    'updated_at' => $date->copy()->addMinutes(rand(0, 1439)),
                ];
            }
        }

        // Bulk insert ile hızlı ekleme
        VcardVisit::insert($visits);

        $this->command->info('VCard visit test data created successfully!');
    }

    private function generateRandomIp(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ];

        return $userAgents[array_rand($userAgents)];
    }
}
