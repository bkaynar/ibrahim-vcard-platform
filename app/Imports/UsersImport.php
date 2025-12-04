<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class UsersImport implements ToModel, WithHeadingRow, SkipsOnError, WithChunkReading, WithBatchInserts, ShouldQueue
{
    use SkipsErrors;

    private $importedCount = 0;
    private $skippedCount = 0;
    private $customErrors = [];
    private $processedEmails = [];
    private $processedPhones = [];
    private $processedUsernames = [];
    private $existingEmails = [];
    private $existingPhones = [];
    private $existingUsernames = [];

    /**
     * Constructor - mevcut verileri yükle
     */
    public function __construct()
    {
        // Mevcut e-posta, telefon ve username'leri bir kez yükle
        $this->existingEmails = User::whereNotNull('email')->pluck('email')->toArray();
        $this->existingPhones = User::whereNotNull('phone')->pluck('phone')->toArray();
        $this->existingUsernames = User::pluck('username')->toArray();
    }

    /**
     * Excel satırından kullanıcı modeli oluşturur
     *
     * Sütun Yapısı:
     * sicil_no -> registration_number
     * adi (veya ad) -> name (ilk kısım)
     * soyadi (veya soyad) -> name (ikinci kısım)
     * cep_telefonu (veya telefon) -> phone (opsiyonel)
     * e_posta (veya eposta) -> email (opsiyonel)
     *
     * NOT: Telefon ve e-posta her ikisi de opsiyonel
     */
    public function model(array $row)
    {
        try {
            // Sütun isimlerini normalize et (hem adi hem ad destekle)
            $sicilNo = $row['sicil_no'] ?? null;
            $ad = $row['adi'] ?? $row['ad'] ?? '';
            $soyad = $row['soyadi'] ?? $row['soyad'] ?? '';
            $telefon = $row['cep_telefonu'] ?? $row['telefon'] ?? '';
            $eposta = $row['e_posta'] ?? $row['eposta'] ?? '';

            // Boş satırları atla
            if (empty($sicilNo) && empty($ad) && empty($soyad)) {
                return null;
            }

            // Ad ve soyadı birleştir
            $name = trim($ad . ' ' . $soyad);

            // Excel içinde aynı e-posta veya telefon tekrar ediyorsa atla
            if (!empty($eposta) && in_array($eposta, $this->processedEmails)) {
                $this->skippedCount++;
                return null;
            }

            if (!empty($telefon) && in_array($telefon, $this->processedPhones)) {
                $this->skippedCount++;
                return null;
            }

            // Veritabanında aynı e-posta veya telefon varsa atla (memory'den kontrol et)
            if (!empty($eposta) && in_array($eposta, $this->existingEmails)) {
                $this->skippedCount++;
                return null;
            }

            if (!empty($telefon) && in_array($telefon, $this->existingPhones)) {
                $this->skippedCount++;
                return null;
            }

            // Username oluştur (ad-soyad formatında)
            $username = Str::slug($name);

            // Aynı username varsa sonuna numara ekle (memory'den kontrol et)
            $originalUsername = $username;
            $counter = 1;
            while (in_array($username, $this->existingUsernames) || in_array($username, $this->processedUsernames ?? [])) {
                $username = $originalUsername . '-' . $counter;
                $counter++;
            }

            // Varsayılan şifre: sicil numarası veya "12345678"
            $defaultPassword = !empty($sicilNo) ? $sicilNo : '12345678';

            $userData = [
                'name' => $name,
                'username' => $username,
                'registration_number' => $sicilNo,
                'email' => !empty($eposta) ? $eposta : null,
                'phone' => !empty($telefon) ? $telefon : null,
                'password' => Hash::make($defaultPassword),
            ];

            $user = User::create($userData);

            // Kullanıcıya 'user' rolü ata
            try {
                $user->assignRole('user');
            } catch (\Exception $e) {
                // Role atama hatası - kaydet ama devam et
            }

            // İşlenen e-posta, telefon ve username'leri kaydet
            if (!empty($eposta)) {
                $this->processedEmails[] = $eposta;
                $this->existingEmails[] = $eposta;
            }
            if (!empty($telefon)) {
                $this->processedPhones[] = $telefon;
                $this->existingPhones[] = $telefon;
            }
            $this->processedUsernames[] = $username;
            $this->existingUsernames[] = $username;

            $this->importedCount++;

            return $user;
        } catch (\Exception $e) {
            $this->skippedCount++;
            $errorMsg = "Satır " . ($this->importedCount + 2) . ": " . $e->getMessage();
            $this->customErrors[] = $errorMsg;
            return null;
        }
    }

    /**
     * Excel başlık satırı numarası
     */
    public function headingRow(): int
    {
        return 1; // İlk satır başlık
    }

    /**
     * Chunk size - Her seferinde 500 satır işle
     */
    public function chunkSize(): int
    {
        return 500;
    }

    /**
     * Batch size - Her seferinde kaç satır insert edilecek
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * İçe aktarılan kayıt sayısını döndürür
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * Atlanan kayıt sayısını döndürür
     */
    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    /**
     * Hataları döndürür
     */
    public function getErrors(): array
    {
        return $this->customErrors;
    }
}
