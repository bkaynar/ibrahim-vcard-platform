<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VcardVisit extends Model
{
    use HasFactory;

    protected $table = 'vcard_visits';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    // İlişki: Ziyaret edilen kullanıcı
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
