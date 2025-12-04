<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CompleteUserDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Farklı profillerde örnek kullanıcılar oluştur
        $users = [
            [
                'name' => 'Ayşe Demir',
                'username' => 'aysedemir',
                'email' => 'ayse.demir@example.com',
                'phone' => '+90 532 987 65 43',
                'address' => 'Çankaya Mahallesi, Atatürk Bulvarı No: 45/12, Çankaya/Ankara',
                'bio' => 'UX/UI Tasarımcı ve dijital pazarlama uzmanı. Kullanıcı deneyimi tasarımı, marka kimliği ve sosyal medya stratejileri konularında 6 yıllık deneyimim var. Yaratıcı çözümler üretmeyi ve ekip çalışmasını seviyorum.',
                'socials' => [
                    'instagram' => 'aysedemir_design',
                    'behance' => 'aysedemir',
                    'dribbble' => 'aysedemir_ui',
                    'linkedin' => 'ayse-demir-uxui',
                    'pinterest' => 'aysedemir_inspiration',
                    'twitter' => 'aysedemir_ux',
                ]
            ],
            [
                'name' => 'Mehmet Kaya',
                'username' => 'mehmetkaya',
                'email' => 'mehmet.kaya@example.com',
                'phone' => '+90 505 123 98 76',
                'address' => 'Kozyatağı Mahallesi, Değirmenbahçe Sok. No: 78/3, Kadıköy/İstanbul',
                'bio' => 'Full-stack developer ve startup kurucusu. React, Node.js, Python ve cloud teknolojilerinde uzmanım. Açık kaynak projelere katkıda bulunuyor ve teknoloji topluluklarında aktif rol alıyorum.',
                'socials' => [
                    'github' => 'mehmetkaya',
                    'linkedin' => 'mehmet-kaya-fullstack',
                    'twitter' => 'mehmetkaya_dev',
                    'youtube' => 'MehmetKayaTech',
                    'discord' => 'MehmetK#5678',
                    'stackoverflow' => 'mehmet-kaya',
                ]
            ],
            [
                'name' => 'Zeynep Özkan',
                'username' => 'zeynepozkan',
                'email' => 'zeynep.ozkan@example.com',
                'phone' => '+90 543 456 78 90',
                'address' => 'Alsancak Mahallesi, Cumhuriyet Bulvarı No: 156/8, Konak/İzmir',
                'bio' => 'Dijital pazarlama uzmanı ve içerik üreticisi. Sosyal medya yönetimi, SEO ve e-ticaret konularında 5 yıllık deneyimim bulunuyor. Markaların dijital dünyada başarılı olmalarına yardımcı oluyorum.',
                'socials' => [
                    'instagram' => 'zeynepozkan_digital',
                    'tiktok' => 'zeynepozkan',
                    'youtube' => 'ZeynepOzkanMarketing',
                    'linkedin' => 'zeynep-ozkan-digital',
                    'twitter' => 'zeynepozkan_dm',
                    'facebook' => 'zeynep.ozkan.marketing',
                    'telegram' => 'zeynepozkan_tips',
                ]
            ]
        ];

        foreach ($users as $index => $userData) {
            // ID'si belirli olan kullanıcıları güncelle veya oluştur
            $targetId = $index + 3; // 3, 4, 5 ID'leri için

            $user = User::find($targetId);

            if (!$user) {
                // Kullanıcı yoksa oluştur
                $user = new User();
                $user->id = $targetId;
                $user->name = $userData['name'];
                $user->username = $userData['username'];
                $user->email = $userData['email'];
                $user->password = Hash::make('password123');
                $user->email_verified_at = now();
                $user->save();

                // User rolü ata
                $user->assignRole('user');

                $this->command->info("✅ ID $targetId kullanıcısı oluşturuldu: " . $userData['name']);
            }

            // Kullanıcının verilerini güncelle
            $user->update([
                'name' => $userData['name'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'phone' => $userData['phone'],
                'address' => $userData['address'],
                'bio' => $userData['bio'],
                'socials' => $userData['socials'],
                'email_verified_at' => now(),
            ]);

            $this->command->info("📝 ID $targetId kullanıcısının verileri güncellendi");
            $this->command->info("   📧 Email: " . $user->email);
            $this->command->info("   👤 Username: " . $user->username);
            $this->command->info("   🔗 Sosyal medya: " . count($user->socials ?? []));
            $this->command->line('');
        }

        $this->command->info('🎉 Tüm örnek kullanıcı verileri başarıyla eklendi!');
        $this->command->info('🔑 Tüm kullanıcılar için şifre: password123');
    }
}
