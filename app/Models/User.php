<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles;

    /**
     * Toplu atamaya izin verilen alanlar.
     */
    protected $fillable = [
        'name',
        'username',
        'registration_number',
        'email',
        'password',
        'phone',
        'address',
        'bio',
        'profile_photo',
        'cover_photo',
        'socials',
        'company_info',
        'bank_info',
        'documents',
    ];

    /**
     * Gizli alanlar (JSON response'ta gösterilmez).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Dönüştürülmesi gereken alanlar.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'socials' => 'array',
        'company_info' => 'array',
        'bank_info' => 'array',
        'documents' => 'array',
    ];

    /**
     * JSON response'ta eklenmesi gereken computed alanlar.
     */
    protected $appends = ['main_role'];

    /**
     * Profil fotoğrafı tam URL olarak döner.
     */
    protected function profilePhotoUrl(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->profile_photo
                ? asset("storage/{$this->profile_photo}")
                : asset('images/default-avatar.png')
        );
    }

    /**
     * Kapak fotoğrafı tam URL olarak döner.
     */
    protected function coverPhotoUrl(): Attribute
    {
        return Attribute::get(
            fn() =>
            $this->cover_photo
                ? asset("storage/{$this->cover_photo}")
                : asset('images/default-cover.jpg')
        );
    }

    /**
     * Profil linki (route örneği).
     */
    public function getProfileUrl(): string
    {
        return route('vcard.show', ['registration_number' => $this->registration_number]);
    }

    public function vcardVisits()
    {
        return $this->hasMany(VcardVisit::class);
    }

    /**
     * Bankalar ile ilişki (Many-to-Many)
     */
    public function banks()
    {
        return $this->belongsToMany(Bank::class, 'bank_user')
            ->withPivot('iban', 'account_holder', 'branch', 'is_primary')
            ->withTimestamps();
    }

    /**
     * Ana banka hesabını döndürür
     */
    public function primaryBank()
    {
        return $this->banks()->wherePivot('is_primary', true)->first();
    }

    /**
     * Kullanıcının ana rolünü döner.
     */
    protected function mainRole(): Attribute
    {
        return Attribute::get(
            fn() => $this->getRoleNames()->first() ?? 'user'
        );
    }
}
