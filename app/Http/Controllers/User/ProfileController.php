<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;


class ProfileController extends Controller
{
    public function edit()
    {
        /** @var User $user */
        $user = Auth::user();

        // Aktif bankaları getir
        $banks = Bank::where('is_active', true)->orderBy('name')->get()->map(function($bank) {
            return [
                'id' => $bank->id,
                'name' => $bank->name,
                'code' => $bank->code,
                'color' => $bank->color,
                'logo_url' => $bank->logo_url,
            ];
        });

        // Kullanıcının banka hesaplarını getir
        $userBanks = $user->banks()->get()->map(function($bank) {
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
        });

        return Inertia::render('User/Profile/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'registration_number' => $user->registration_number,
                'email' => $user->email,
                'phone' => $user->phone,
                'address' => $user->address,
                'bio' => $user->bio,
                'profile_photo' => $user->profile_photo,
                'cover_photo' => $user->cover_photo,
                'profile_photo_url' => $user->profile_photo
                    ? asset("storage/{$user->profile_photo}")
                    : asset('images/default-avatar.png'),
                'cover_photo_url' => $user->cover_photo
                    ? asset("storage/{$user->cover_photo}")
                    : asset('images/default-cover.jpg'),
                'socials' => $user->socials ?? [],
                'company_info' => $user->company_info ?? null,
                'documents' => $user->documents ?? [],
            ],
            'banks' => $banks,
            'userBanks' => $userBanks,
        ]);
    }

    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5 MB
            'cover_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'],
            'socials' => ['nullable', 'array'],
            'socials.*.platform' => ['required', 'string', 'max:50'],
            'socials.*.username' => ['required', 'string', 'max:255'],
            // Firma bilgisi
            'company_info' => ['nullable', 'array'],
            'company_info.company_name' => ['nullable', 'string', 'max:255'],
            'company_info.company_title' => ['nullable', 'string', 'max:255'],
            'company_info.tax_office' => ['nullable', 'string', 'max:255'],
            'company_info.tax_number' => ['nullable', 'string', 'max:50'],
            'company_info.company_address' => ['nullable', 'string', 'max:500'],
            // Banka hesapları (yeni sistem)
            'bank_accounts' => ['nullable', 'array'],
            'bank_accounts.*.bank_id' => ['required', 'exists:banks,id'],
            'bank_accounts.*.iban' => ['nullable', 'string', 'max:34'],
            'bank_accounts.*.account_holder' => ['nullable', 'string', 'max:255'],
            'bank_accounts.*.branch' => ['nullable', 'string', 'max:100'],
            'bank_accounts.*.is_primary' => ['boolean'],
            // Dokümanlar
            'documents' => ['nullable', 'array'],
            'documents.*.title' => ['required', 'string', 'max:255'],
            'documents.*.file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'], // 10 MB
        ]);

        // ImageManager oluştur (her iki işlemde de kullanacağız)
        $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

        // Profil fotoğrafı yükleme
        if ($request->hasFile('profile_photo')) {
            // Eski fotoğrafı sil
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';

            try {
                // Yeni API: read -> encode
                $image = $manager->read($file->getPathname())->encode(new WebpEncoder(), 80);

                $path = 'profile-photos/' . $filename;
                Storage::disk('public')->put($path, (string)$image);

                $user->profile_photo = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        // Kapak fotoğrafı yükleme
        if ($request->hasFile('cover_photo')) {
            // Eski fotoğrafı sil
            if ($user->cover_photo) {
                Storage::disk('public')->delete($user->cover_photo);
            }

            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';

            try {
                $image = $manager->read($file->getPathname())->encode(new WebpEncoder(), 80);

                $path = 'cover-photos/' . $filename;
                Storage::disk('public')->put($path, (string)$image);

                $user->cover_photo = $path;
            } catch (\Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        // Sosyal medya verilerini işle
        $socialsArray = [];
        if ($request->has('socials') && is_array($request->socials)) {
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socialsArray[$social['platform']] = $social['username'];
                }
            }
        }

        // Firma bilgilerini işle
        $companyInfo = null;
        if ($request->has('company_info') && is_array($request->company_info)) {
            $company = $request->company_info;
            if (!empty($company['company_name']) || !empty($company['tax_office']) ||
                !empty($company['tax_number']) || !empty($company['company_address']) || !empty($company['company_title'])) {
                $companyInfo = [
                    'company_name' => $company['company_name'] ?? '',
                    'company_title' => $company['company_title'] ?? '',
                    'tax_office' => $company['tax_office'] ?? '',
                    'tax_number' => $company['tax_number'] ?? '',
                    'company_address' => $company['company_address'] ?? '',
                ];
            }
        }

        // Banka hesaplarını işle - pivot tablosunu güncelle
        if ($request->has('bank_accounts') && is_array($request->bank_accounts)) {
            $bankAccountsToSync = [];
            foreach ($request->bank_accounts as $account) {
                if (!empty($account['bank_id'])) {
                    $bankAccountsToSync[$account['bank_id']] = [
                        'iban' => $account['iban'] ?? null,
                        'account_holder' => $account['account_holder'] ?? null,
                        'branch' => $account['branch'] ?? null,
                        'is_primary' => $account['is_primary'] ?? false,
                    ];
                }
            }

            // Sync ile mevcut banka hesaplarını güncelle
            $user->banks()->sync($bankAccountsToSync);
        } else {
            // Banka hesabı yoksa tümünü sil
            $user->banks()->detach();
        }

        // Dokümanları işle
        $documentsArray = [];
        if ($request->has('documents') && is_array($request->documents)) {
            foreach ($request->documents as $doc) {
                if (!empty($doc['title'])) {
                    $documentEntry = ['title' => $doc['title']];

                    // Yeni dosya yüklendiyse
                    if (isset($doc['file']) && $doc['file'] instanceof \Illuminate\Http\UploadedFile) {
                        $file = $doc['file'];
                        $filename = uniqid('doc_') . '.pdf';
                        $path = 'documents/' . $filename;

                        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
                        $documentEntry['file_path'] = $path;
                    }
                    // Mevcut dosya varsa koru
                    elseif (isset($doc['file_path'])) {
                        $documentEntry['file_path'] = $doc['file_path'];
                    }

                    // Sadece dosya yolu varsa ekle (boş doküman eklemeyi önle)
                    if (isset($documentEntry['file_path'])) {
                        $documentsArray[] = $documentEntry;
                    }
                }
            }
        }

        // Kullanıcı bilgilerini güncelle (username hariç)
        $user->update([
            'name' => $request->name,
            'registration_number' => $request->registration_number,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'bio' => $request->bio,
            'socials' => $socialsArray,
            'company_info' => $companyInfo,
            'documents' => $documentsArray,
        ]);

        return redirect()->route('user.profile.edit')->with('success', 'Profil başarıyla güncellendi!');
    }

    public function destroyProfilePhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
            $user->update(['profile_photo' => null]);
        }

        return redirect()->route('user.profile.edit')->with('success', 'Profil fotoğrafı silindi!');
    }

    public function destroyCoverPhoto()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->cover_photo) {
            Storage::disk('public')->delete($user->cover_photo);
            $user->update(['cover_photo' => null]);
        }

        return redirect()->route('user.profile.edit')->with('success', 'Kapak fotoğrafı silindi!');
    }
}
