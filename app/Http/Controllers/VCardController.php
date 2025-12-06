<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\VcardVisit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VCardController extends Controller
{
    public function show(Request $request, $registration_number)
    {
        // Kullanıcıyı registration_number ile bul
        $user = User::where('registration_number', $registration_number)->firstOrFail();

        // Ziyaret kaydı oluştur
        VcardVisit::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'visited_at' => now(),
        ]);

        return Inertia::render('VCard/Show', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'registration_number' => $user->registration_number,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,
                'profile_photo_url' => $user->profile_photo
                    ? asset("storage/{$user->profile_photo}")
                    : asset('images/default-avatar.png'),
                'cover_photo_url' => $user->cover_photo
                    ? asset("storage/{$user->cover_photo}")
                    : asset('images/default-cover.jpg'),
                'socials' => $user->socials,
                'company_info' => $user->company_info,
                // Kullanıcının banka hesaplarını pivot tablosundan al
                'userBanks' => $user->banks()->get()->map(function($bank) {
                    return [
                        'bank_id' => $bank->id,
                        'bank_name' => $bank->name,
                        'bank_code' => $bank->code,
                        'bank_color' => $bank->color,
                        'bank_logo_url' => $bank->logo_url,
                        'iban' => $bank->pivot->iban,
                        'account_holder' => $bank->pivot->account_holder,
                        'branch' => $bank->pivot->branch,
                        'is_primary' => $bank->pivot->is_primary,
                    ];
                })->toArray(),
                // backward compat: keep single bank_info if present on user model
                'bank_info' => $user->bank_info ?? [],
                'documents' => $user->documents ? array_map(function($doc) {
                    if (isset($doc['file_path'])) {
                        $doc['file_url'] = asset("storage/{$doc['file_path']}");
                    }
                    return $doc;
                }, $user->documents) : [],
            ]
        ]);
    }
}
