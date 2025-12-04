<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ID'si 3 olan kullanıcıyı bul veya oluştur
        $user = User::find(3);

        if (!$user) {
            // Eğer ID 3 olan kullanıcı yoksa oluştur
            $user = User::create([
                'name' => 'Ahmet Yılmaz',
                'username' => 'ahmetyilmaz',
                'email' => 'ahmet@example.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]);

            // User rolü ata
            $user->assignRole('user');
        }

        // Kullanıcıya tam veri ekle (fotoğraflar hariç)
        $user->update([
            'name' => 'Ahmet Yılmaz',
            'username' => 'ahmetyilmaz',
            'email' => 'ahmet.yilmaz@example.com',
            'phone' => '+90 555 123 45 67',
            'address' => 'Kadıköy Mahallesi, Bağdat Caddesi No: 123/5, Kadıköy/İstanbul',
            'bio' => 'Yazılım geliştirici ve teknoloji tutkunu. 8 yıllık deneyimle web teknolojileri, mobil uygulama geliştirme ve veri analizi alanlarında uzmanım. Açık kaynak projelere katkıda bulunmayı ve yeni teknolojiler öğrenmeyi seviyorum.',
            'socials' => [
                'instagram' => 'ahmetyilmaz_dev',
                'twitter' => 'ahmetyilmaz93',
                'linkedin' => 'ahmet-yilmaz-developer',
                'github' => 'ahmetyilmaz',
                'youtube' => 'AhmetYilmazTech',
                'facebook' => 'ahmet.yilmaz.developer',
                'behance' => 'ahmetyilmaz_design',
                'dribbble' => 'ahmetyilmaz',
                'telegram' => 'ahmetyilmaz_dev',
                'discord' => 'AhmetY#1234',
            ],
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ ID 3 kullanıcısına tam veri eklendi!');
        $this->command->info('📧 Email: ' . $user->email);
        $this->command->info('👤 Username: ' . $user->username);
        $this->command->info('🔗 Sosyal medya hesapları: ' . count($user->socials ?? []));
    }
}
