<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Intervention\Image\ImageManager;
use Intervention\Image\Facades\Image;
use Exception;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::role('user')
            ->with('roles')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('registration_number', 'like', "%{$search}%");
                });
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:5120',   // 5 MB
            'cover_photo'   => 'nullable|image|max:5120',   // 5 MB
            'socials' => 'nullable|array',
            'socials.*.platform' => 'required_with:socials.*.username|string|max:50',
            'socials.*.username' => 'required_with:socials.*.platform|string|max:255',
        ]);

        $data = $request->only(['name', 'username', 'registration_number', 'email', 'phone', 'address', 'bio']);
        $data['password'] = Hash::make($request->password);

        // Sosyal medya verilerini işle
        if ($request->has('socials') && is_array($request->socials)) {
            $socials = [];
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socials[$social['platform']] = $social['username'];
                }
            }
            $data['socials'] = $socials;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            // Webp olarak dönüştür ve public diskine kaydet
            try {

                $image = $manager->read($file->getPathname())
                    ->cover(512, 512) // örnek boyut vs. istersen
                    ->toWebp(80);     // kalite

                // 'profile-photos' klasörüne kaydet
                $path = 'profile-photos/' . $filename;
                Storage::disk('public')->put($path, (string) $image);

                $data['profile_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            try {
                $image = $manager->read($file->getPathname())
                    ->cover(1920, 1080) // örnek boyut vs. istersen
                    ->toWebp(80);       // kalite

                $path = 'cover-photos/' . $filename;
                Storage::disk('public')->put($path, (string) $image);

                $data['cover_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        $user = User::create($data);

        $user->assignRole('user');

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla oluşturuldu!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load('roles');
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'registration_number' => 'nullable|string|max:50|unique:users,registration_number,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_photo' => 'nullable|image|max:5120',   // 5 MB
            'cover_photo'   => 'nullable|image|max:5120',   // 5 MB
            'socials' => 'nullable|array',
            'socials.*.platform' => 'required_with:socials.*.username|string|max:50',
            'socials.*.username' => 'required_with:socials.*.platform|string|max:255',
        ]);

        $data = $request->only(['name', 'username', 'registration_number', 'email', 'phone', 'address', 'bio']);

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        // Sosyal medya verilerini işle
        if ($request->has('socials') && is_array($request->socials)) {
            $socials = [];
            foreach ($request->socials as $social) {
                if (!empty($social['platform']) && !empty($social['username'])) {
                    $socials[$social['platform']] = $social['username'];
                }
            }
            $data['socials'] = $socials;
        }

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = uniqid('profile_') . '.webp';
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            // Webp olarak dönüştür ve public diskine kaydet
            try {
                $image = $manager->read($file->getPathname())
                    ->cover(512, 512) // örnek boyut vs. istersen
                    ->toWebp(80);     // kalite

                // 'profile-photos' klasörüne kaydet
                $path = 'profile-photos/' . $filename;
                Storage::disk('public')->put($path, (string) $image);

                $data['profile_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['profile_photo' => 'Profil fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('cover_photo')) {
            $file = $request->file('cover_photo');
            $filename = uniqid('cover_') . '.webp';
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver()
            );
            try {
                $image = $manager->read($file->getPathname())
                    ->cover(1920, 1080) // örnek boyut vs. istersen
                    ->toWebp(80);       // kalite

                $path = 'cover-photos/' . $filename;
                Storage::disk('public')->put($path, (string) $image);

                $data['cover_photo'] = $path;
            } catch (Exception $e) {
                return back()->withErrors(['cover_photo' => 'Kapak fotoğrafı işlenirken bir hata oluştu: ' . $e->getMessage()]);
            }
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Kullanıcı başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index');
    }

    /**
     * Excel'den toplu kullanıcı içe aktarma sayfası
     */
    public function importForm()
    {
        return Inertia::render('Admin/Users/Import');
    }

    /**
     * Excel'den toplu kullanıcı içe aktarma işlemi
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240', // 10MB
        ]);

        // PHP timeout süresini artır (5 dakika)
        set_time_limit(300);
        ini_set('max_execution_time', 300);

        try {
            $import = new UsersImport();

            try {
                Excel::import($import, $request->file('file'));
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                $failures = $e->failures();
                $errorMessages = [];

                foreach ($failures as $failure) {
                    $row = $failure->row();
                    $attribute = $failure->attribute();
                    $errors = $failure->errors();
                    $values = $failure->values();

                    // Her hata için detaylı mesaj oluştur
                    foreach ($errors as $error) {
                        $value = isset($values[$attribute]) ? $values[$attribute] : 'boş';
                        $errorMessages[] = "Satır {$row} - {$attribute}: {$error} (Değer: {$value})";
                    }
                }

                // İlk 50 hatayı göster
                $displayErrors = array_slice($errorMessages, 0, 50);
                if (count($errorMessages) > 50) {
                    $displayErrors[] = "... ve " . (count($errorMessages) - 50) . " hata daha var.";
                }

                \Log::error('Import validation errors: ' . json_encode($errorMessages));

                return redirect()->route('admin.users.import.form')
                    ->with('error', count($errorMessages) . ' adet validation hatası oluştu')
                    ->with('import_errors', $displayErrors);
            }

            $importedCount = $import->getImportedCount();
            $skippedCount = $import->getSkippedCount();
            $importErrors = $import->getErrors();

            $message = "{$importedCount} kullanıcı başarıyla içe aktarıldı.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} kayıt atlandı.";
            }

            if (!empty($importErrors)) {
                return redirect()->route('admin.users.import.form')
                    ->with('success', $message)
                    ->with('import_errors', $importErrors);
            }

            return redirect()->route('admin.users.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.users.import.form')
                ->with('error', 'İçe aktarma sırasında bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Örnek Excel şablonunu indir
     */
    public function downloadTemplate()
    {
        $filePath = public_path('templates/kullanici-import-sablonu.xlsx');

        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Şablon dosyası bulunamadı.');
        }

        return response()->download($filePath, 'kullanici-import-sablonu.xlsx');
    }
}
