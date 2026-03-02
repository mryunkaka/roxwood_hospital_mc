<?php

namespace App\Http\Controllers;

use App\Models\MedicalRegulation;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RegulasiController extends Controller
{
    private function guardNonStaff(): void
    {
        $user = session('user', []);
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if ($role === 'staff') {
            abort(403);
        }
    }

    public function index()
    {
        return redirect()->route('medis.regulasi');
    }

    public function medis()
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $regs = MedicalRegulation::query()
            ->select([
                'id',
                'category',
                'code',
                'name',
                'location',
                'price_type',
                'price_min',
                'price_max',
                'payment_type',
                'duration_minutes',
                'notes',
                'is_active',
            ])
            ->orderBy('category')
            ->orderBy('code')
            ->get();

        return view('pages.medis.regulasi', [
            'regs' => $regs,
        ]);
    }

    public function farmasi()
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $packages = Package::query()
            ->select(['id', 'name', 'bandage_qty', 'ifaks_qty', 'painkiller_qty', 'price'])
            ->orderBy('name')
            ->get();

        return view('pages.farmasi.regulasi', [
            'packages' => $packages,
        ]);
    }

    public function updatePackage(Request $request, Package $package)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bandage_qty' => ['required', 'integer', 'min:0'],
            'ifaks_qty' => ['required', 'integer', 'min:0'],
            'painkiller_qty' => ['required', 'integer', 'min:0'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $package->update([
            'name' => trim((string) $validated['name']),
            'bandage_qty' => (int) $validated['bandage_qty'],
            'ifaks_qty' => (int) $validated['ifaks_qty'],
            'painkiller_qty' => (int) $validated['painkiller_qty'],
            'price' => (int) $validated['price'],
        ]);

        return response()->json([
            'success' => true,
            'row' => [
                'id' => (int) $package->id,
                'name' => (string) $package->name,
                'bandage_qty' => (int) $package->bandage_qty,
                'ifaks_qty' => (int) $package->ifaks_qty,
                'painkiller_qty' => (int) $package->painkiller_qty,
                'price' => (int) $package->price,
            ],
        ]);
    }

    public function updateRegulation(Request $request, MedicalRegulation $medicalRegulation)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $validated = $request->validate([
            'category' => ['required', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:50'],
            'price_type' => ['required', 'in:FIXED,RANGE'],
            'price_min' => ['required', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0'],
            'payment_type' => ['required', 'in:CASH,INVOICE,BILLING'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $priceMin = (int) $validated['price_min'];
        $priceMax = isset($validated['price_max']) ? (int) $validated['price_max'] : 0;

        if ((string) $validated['price_type'] === 'FIXED') {
            $priceMax = $priceMin;
        } elseif ($priceMax < $priceMin) {
            $priceMax = $priceMin;
        }

        $medicalRegulation->update([
            'category' => trim((string) $validated['category']),
            'name' => trim((string) $validated['name']),
            'location' => ($validated['location'] ?? null) !== null && trim((string) $validated['location']) !== '' ? trim((string) $validated['location']) : null,
            'price_type' => (string) $validated['price_type'],
            'price_min' => $priceMin,
            'price_max' => $priceMax,
            'payment_type' => (string) $validated['payment_type'],
            'duration_minutes' => ($validated['duration_minutes'] ?? null) !== null ? (int) $validated['duration_minutes'] : null,
            'notes' => ($validated['notes'] ?? null) !== null && trim((string) $validated['notes']) !== '' ? trim((string) $validated['notes']) : null,
            'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : false,
        ]);

        return response()->json([
            'success' => true,
            'row' => [
                'id' => (int) $medicalRegulation->id,
                'category' => (string) $medicalRegulation->category,
                'code' => (string) $medicalRegulation->code,
                'name' => (string) $medicalRegulation->name,
                'location' => $medicalRegulation->location !== null ? (string) $medicalRegulation->location : null,
                'price_type' => (string) $medicalRegulation->price_type,
                'price_min' => (int) $medicalRegulation->price_min,
                'price_max' => (int) $medicalRegulation->price_max,
                'payment_type' => (string) $medicalRegulation->payment_type,
                'duration_minutes' => $medicalRegulation->duration_minutes !== null ? (int) $medicalRegulation->duration_minutes : null,
                'notes' => $medicalRegulation->notes !== null ? (string) $medicalRegulation->notes : null,
                'is_active' => (bool) $medicalRegulation->is_active,
            ],
        ]);
    }
}
