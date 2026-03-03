<?php

namespace App\Http\Controllers;

use App\Models\UserRh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ValidasiController extends Controller
{
    private function guardNonStaff(): void
    {
        $user = session('user', []);
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if ($role === 'staff') {
            abort(403);
        }
    }

    private function currentUser(): ?UserRh
    {
        $id = session('user.id');
        if (!$id) {
            return null;
        }

        return UserRh::query()->find($id);
    }

    public function index()
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $select = [
            'id',
            'full_name',
            'citizen_id',
            'no_hp_ic',
            'jenis_kelamin',
            'role',
            'batch',
            'kode_nomor_induk_rs',
            'position',
            'tanggal_masuk',
            'file_ktp',
            'file_sim',
            'file_kta',
            'file_skb',
            'sertifikat_heli',
            'sertifikat_operasi',
            'dokumen_lainnya',
            'is_verified',
            'is_active',
            'resigned_at',
            'created_at',
        ];

        static $hasPhotoProfile = null;
        if ($hasPhotoProfile === null) {
            $hasPhotoProfile = Schema::hasColumn('user_rh', 'photo_profile');
        }
        if ($hasPhotoProfile) {
            $select[] = 'photo_profile';
        }

        $users = UserRh::query()
            ->select($select)
            ->orderByRaw('is_active ASC')
            ->orderByDesc('created_at')
            ->get();

        // Normalize dates for the frontend (avoid timezone-shifted ISO strings in the UI).
        $users = $users->map(function (UserRh $u) {
            $u->tanggal_masuk = $u->tanggal_masuk ? $u->tanggal_masuk->format('Y-m-d') : null;
            $u->resigned_at = $u->resigned_at ? $u->resigned_at->toIso8601String() : null;
            return $u;
        });

        return view('pages.validasi.index', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        if (!$current) {
            abort(403);
        }

        if (!$current->canManage($userRh)) {
            abort(403);
        }

        $validRoles = [
            'Staff',
            'Staff Manager',
            'Lead Manager',
            'Head Manager',
            'Vice Director',
            'Director',
        ];

        $validated = $request->validate([
            'role' => ['nullable', 'string', Rule::in($validRoles)],
            'position' => ['nullable', 'string', 'max:100'],
            'is_verified' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $roleHierarchy = [
            'Staff' => 1,
            'Staff Manager' => 2,
            'Lead Manager' => 3,
            'Head Manager' => 4,
            'Vice Director' => 5,
            'Director' => 6,
        ];

        $targetRole = isset($validated['role']) ? (string) $validated['role'] : (string) $userRh->role;
        if (($roleHierarchy[$current->role] ?? 0) <= ($roleHierarchy[$targetRole] ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.validation_role_not_allowed'),
            ], 422);
        }

        $userRh->update([
            'role' => $targetRole,
            'position' => isset($validated['position']) && trim((string) $validated['position']) !== '' ? trim((string) $validated['position']) : $userRh->position,
            'is_verified' => isset($validated['is_verified']) ? (bool) $validated['is_verified'] : $userRh->is_verified,
            'is_active' => isset($validated['is_active']) ? (bool) $validated['is_active'] : $userRh->is_active,
        ]);

        return response()->json([
            'success' => true,
            'row' => [
                'id' => (int) $userRh->id,
                'full_name' => (string) $userRh->full_name,
                'citizen_id' => (string) ($userRh->citizen_id ?? ''),
                'no_hp_ic' => (string) ($userRh->no_hp_ic ?? ''),
                'jenis_kelamin' => (string) ($userRh->jenis_kelamin ?? ''),
                'role' => (string) ($userRh->role ?? ''),
                'batch' => (int) ($userRh->batch ?? 0),
                'kode_nomor_induk_rs' => (string) ($userRh->kode_nomor_induk_rs ?? ''),
                'position' => (string) ($userRh->position ?? ''),
                'tanggal_masuk' => $userRh->tanggal_masuk ? $userRh->tanggal_masuk->format('Y-m-d') : null,
                'photo_profile' => (string) ($userRh->photo_profile ?? ''),
                'file_ktp' => (string) ($userRh->file_ktp ?? ''),
                'file_sim' => (string) ($userRh->file_sim ?? ''),
                'file_kta' => (string) ($userRh->file_kta ?? ''),
                'file_skb' => (string) ($userRh->file_skb ?? ''),
                'sertifikat_heli' => (string) ($userRh->sertifikat_heli ?? ''),
                'sertifikat_operasi' => (string) ($userRh->sertifikat_operasi ?? ''),
                'dokumen_lainnya' => (string) ($userRh->dokumen_lainnya ?? ''),
                'is_verified' => (bool) $userRh->is_verified,
                'is_active' => (bool) $userRh->is_active,
                'created_at' => $userRh->created_at?->toIso8601String(),
            ],
        ]);
    }
}
