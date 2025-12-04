# User Data Seeders

Bu proje için oluşturulmuş kullanıcı veri seeder'ları.

## Seeder'ları Çalıştırma

### 1. Tek Kullanıcı (ID: 3) için Veri Seeder'ı

```bash
php artisan db:seed --class=UserDataSeeder
```

Bu komut ID'si 3 olan kullanıcıya aşağıdaki verileri ekler:

- ✅ Tam profil bilgileri
- ✅ Telefon numarası
- ✅ Adres bilgisi
- ✅ Detaylı biyografi
- ✅ 10 farklı sosyal medya hesabı
- ❌ Profil fotoğrafı (manuel yüklenecek)
- ❌ Kapak fotoğrafı (manuel yüklenecek)

### 2. Çoklu Kullanıcı Veri Seeder'ı

```bash
php artisan db:seed --class=CompleteUserDataSeeder
```

Bu komut 3 farklı kullanıcı profili oluşturur (ID: 3, 4, 5):

- 👩‍💼 **Ayşe Demir** - UX/UI Tasarımcı
- 👨‍💻 **Mehmet Kaya** - Full-stack Developer
- 👩‍💼 **Zeynep Özkan** - Dijital Pazarlama Uzmanı

### 3. Tüm Seeder'ları Çalıştırma

```bash
php artisan db:seed
```

## Örnek Kullanıcı Bilgileri

### Ahmet Yılmaz (ID: 3)

- **Email:** ahmet.yilmaz@example.com
- **Username:** ahmetyilmaz
- **Şifre:** password123
- **Meslek:** Yazılım Geliştirici
- **Sosyal Medya:** 10 platform

### Ayşe Demir (ID: 4)

- **Email:** ayse.demir@example.com
- **Username:** aysedemir
- **Şifre:** password123
- **Meslek:** UX/UI Tasarımcı
- **Sosyal Medya:** 6 platform

### Mehmet Kaya (ID: 5)

- **Email:** mehmet.kaya@example.com
- **Username:** mehmetkaya
- **Şifre:** password123
- **Meslek:** Full-stack Developer
- **Sosyal Medya:** 6 platform

### Zeynep Özkan (ID: 6)

- **Email:** zeynep.ozkan@example.com
- **Username:** zeynepozkan
- **Şifre:** password123
- **Meslek:** Dijital Pazarlama Uzmanı
- **Sosyal Medya:** 7 platform

## Veritabanını Sıfırlama ve Yeniden Seeding

```bash
php artisan migrate:fresh --seed
```

## Not

Fotoğraflar seeder'larda eklenmemiştir. Admin panelinden manuel olarak yüklenebilir.
