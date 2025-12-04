<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    public function run(): void
    {
        $banks = [
            [
                'name' => 'Ziraat Bankası',
                'code' => 'ziraat',
                'color' => '#16A34A', // green-600
                'is_active' => true,
            ],
            [
                'name' => 'Halkbank',
                'code' => 'halk',
                'color' => '#2563EB', // blue-600
                'is_active' => true,
            ],
            [
                'name' => 'Vakıfbank',
                'code' => 'vakif',
                'color' => '#EA580C', // orange-600
                'is_active' => true,
            ],
            [
                'name' => 'Garanti BBVA',
                'code' => 'garanti',
                'color' => '#15803D', // green-700
                'is_active' => true,
            ],
            [
                'name' => 'Yapı Kredi',
                'code' => 'yapikredi',
                'color' => '#1D4ED8', // blue-700
                'is_active' => true,
            ],
            [
                'name' => 'Akbank',
                'code' => 'akbank',
                'color' => '#DC2626', // red-600
                'is_active' => true,
            ],
            [
                'name' => 'İş Bankası',
                'code' => 'isbank',
                'color' => '#1E40AF', // blue-800
                'is_active' => true,
            ],
            [
                'name' => 'Denizbank',
                'code' => 'denizbank',
                'color' => '#22C55E', // green-500
                'is_active' => true,
            ],
            [
                'name' => 'TEB',
                'code' => 'teb',
                'color' => '#4F46E5', // indigo-600
                'is_active' => true,
            ],
            [
                'name' => 'ING',
                'code' => 'ing',
                'color' => '#F97316', // orange-500
                'is_active' => true,
            ],
            [
                'name' => 'Kuveyt Türk',
                'code' => 'kuveyt',
                'color' => '#047857', // emerald-700
                'is_active' => true,
            ],
            [
                'name' => 'Albaraka Türk',
                'code' => 'albaraka',
                'color' => '#0D9488', // teal-600
                'is_active' => true,
            ],
            [
                'name' => 'QNB Finansbank',
                'code' => 'qnb',
                'color' => '#7C3AED', // violet-600
                'is_active' => true,
            ],
            [
                'name' => 'Şekerbank',
                'code' => 'sekerbank',
                'color' => '#0891B2', // cyan-600
                'is_active' => true,
            ],
        ];

        foreach ($banks as $bank) {
            Bank::updateOrCreate(
                ['code' => $bank['code']],
                $bank
            );
        }
    }
}
