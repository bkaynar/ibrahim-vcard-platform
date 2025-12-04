<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $banks = Bank::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Banks/Index', [
            'banks' => $banks,
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
        return Inertia::render('Admin/Banks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:banks,code',
            'color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'code', 'color', 'is_active']);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('bank-logos', 'public');
            $data['logo'] = $path;
        }

        Bank::create($data);

        return redirect()->route('admin.banks.index')->with('success', 'Banka başarıyla oluşturuldu!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return Inertia::render('Admin/Banks/Edit', [
            'bank' => $bank,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:banks,code,' . $bank->id,
            'color' => 'required|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['name', 'code', 'color', 'is_active']);

        if ($request->hasFile('logo')) {
            // Eski logoyu sil
            if ($bank->logo) {
                Storage::disk('public')->delete($bank->logo);
            }

            $path = $request->file('logo')->store('bank-logos', 'public');
            $data['logo'] = $path;
        }

        $bank->update($data);

        return redirect()->route('admin.banks.index')->with('success', 'Banka başarıyla güncellendi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        // Logo varsa sil
        if ($bank->logo) {
            Storage::disk('public')->delete($bank->logo);
        }

        $bank->delete();

        return redirect()->route('admin.banks.index')->with('success', 'Banka başarıyla silindi!');
    }
}
