<?php

namespace App\Http\Controllers;

use App\Models\UserRh;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ManageUsersController extends Controller
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

    private function roleHierarchy(): array
    {
        return [
            'Staff' => 1,
            'Staff Manager' => 2,
            'Lead Manager' => 3,
            'Head Manager' => 4,
            'Vice Director' => 5,
            'Director' => 6,
        ];
    }

    private function validRoles(): array
    {
        return array_keys($this->roleHierarchy());
    }

    private function ensureCanManage(?UserRh $current, UserRh $target): void
    {
        if (!$current) {
            abort(403);
        }

        if ((int) $current->id === (int) $target->id) {
            abort(403);
        }

        if (!$current->canManage($target)) {
            abort(403);
        }
    }

    private function presentUser(UserRh $u, ?UserRh $current = null): array
    {
        $tanggalMasuk = $u->tanggal_masuk ? $u->tanggal_masuk->format('Y-m-d') : null;
        $resignedAt = $u->resigned_at ? $u->resigned_at->toIso8601String() : null;
        $reactivatedAt = $u->reactivated_at ? $u->reactivated_at->toIso8601String() : null;

        $resignedByName = (string) ($u->getAttribute('resigned_by_name') ?? '');
        $reactivatedByName = (string) ($u->getAttribute('reactivated_by_name') ?? '');

        return [
            'id' => (int) $u->id,
            'full_name' => (string) $u->full_name,
            'position' => (string) ($u->position ?? ''),
            'role' => (string) ($u->role ?? ''),
            'is_active' => (bool) $u->is_active,
            'tanggal_masuk' => $tanggalMasuk,
            'batch' => $u->batch === null ? null : (int) $u->batch,
            'kode_nomor_induk_rs' => (string) ($u->kode_nomor_induk_rs ?? ''),
            'sertifikat_heli' => (string) ($u->sertifikat_heli ?? ''),
            'sertifikat_operasi' => (string) ($u->sertifikat_operasi ?? ''),
            'dokumen_lainnya' => (string) ($u->dokumen_lainnya ?? ''),
            'resign_reason' => (string) ($u->resign_reason ?? ''),
            'resigned_at' => $resignedAt,
            'resigned_by_name' => $resignedByName,
            'reactivated_at' => $reactivatedAt,
            'reactivated_note' => (string) ($u->reactivated_note ?? ''),
            'reactivated_by_name' => $reactivatedByName,
            'can_manage' => $current ? $current->canManage($u) : false,
            'is_self' => $current ? ((int) $current->id === (int) $u->id) : false,
        ];
    }

    public function index()
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();

        $users = UserRh::query()
            ->from('user_rh as u')
            ->leftJoin('user_rh as r', 'r.id', '=', 'u.resigned_by')
            ->leftJoin('user_rh as ra', 'ra.id', '=', 'u.reactivated_by')
            ->select([
                'u.*',
                'r.full_name as resigned_by_name',
                'ra.full_name as reactivated_by_name',
            ])
            ->orderByDesc('u.is_active')
            ->orderBy('u.full_name')
            ->get();

        $presented = $users->map(fn (UserRh $u) => $this->presentUser($u, $current))->values();

        return view('pages.users.index', [
            'users' => $presented,
        ]);
    }

    public function store(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        if (!$current) {
            abort(403);
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100', Rule::unique('user_rh', 'full_name')],
            'position' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', Rule::in($this->validRoles())],
            'batch' => ['nullable', 'integer', 'min:1', 'max:26'],
        ]);

        $roleHierarchy = $this->roleHierarchy();
        $targetRole = (string) $validated['role'];
        if (($roleHierarchy[$current->role] ?? 0) <= ($roleHierarchy[$targetRole] ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.manage_users_role_not_allowed'),
            ], 422);
        }

        $fullName = ucwords(strtolower((string) $validated['full_name']));

        $user = UserRh::query()->create([
            'full_name' => $fullName,
            'position' => trim((string) $validated['position']),
            'role' => $targetRole,
            'batch' => $validated['batch'] ?? null,
            'pin' => Hash::make('0000'),
            'is_active' => true,
            'is_verified' => true,
        ]);

        return response()->json([
            'success' => true,
            'row' => $this->presentUser($user, $current),
        ]);
    }

    public function update(Request $request, UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        $this->ensureCanManage($current, $userRh);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:100', Rule::unique('user_rh', 'full_name')->ignore($userRh->id)],
            'position' => ['required', 'string', 'max:100'],
            'role' => ['required', 'string', Rule::in($this->validRoles())],
            'batch' => ['nullable', 'integer', 'min:1', 'max:26'],
            'new_pin' => ['nullable', 'string', 'size:4', 'regex:/^[0-9]{4}$/'],
        ]);

        $roleHierarchy = $this->roleHierarchy();
        $targetRole = (string) $validated['role'];
        if (($roleHierarchy[$current->role] ?? 0) <= ($roleHierarchy[$targetRole] ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => __('messages.manage_users_role_not_allowed'),
            ], 422);
        }

        $userRh->fill([
            'full_name' => ucwords(strtolower((string) $validated['full_name'])),
            'position' => trim((string) $validated['position']),
            'role' => $targetRole,
            'batch' => $validated['batch'] ?? null,
        ]);

        if (!empty($validated['new_pin'])) {
            $userRh->pin = Hash::make((string) $validated['new_pin']);
        }

        $userRh->save();

        return response()->json([
            'success' => true,
            'row' => $this->presentUser($userRh->fresh(), $current),
        ]);
    }

    public function deleteKodeMedis(UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        $this->ensureCanManage($current, $userRh);

        $userRh->update([
            'kode_nomor_induk_rs' => null,
        ]);

        return response()->json([
            'success' => true,
        ]);
    }

    public function resign(Request $request, UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        $this->ensureCanManage($current, $userRh);

        $validated = $request->validate([
            'resign_reason' => ['required', 'string', 'max:2000'],
        ]);

        $userRh->update([
            'is_active' => false,
            'resign_reason' => trim((string) $validated['resign_reason']),
            'resigned_by' => (int) $current->id,
            'resigned_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'row' => $this->presentUser($userRh->fresh(), $current),
        ]);
    }

    public function reactivate(Request $request, UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        $this->ensureCanManage($current, $userRh);

        $validated = $request->validate([
            'reactivated_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $userRh->update([
            'is_active' => true,
            'reactivated_at' => now(),
            'reactivated_by' => (int) $current->id,
            'reactivated_note' => isset($validated['reactivated_note']) ? trim((string) $validated['reactivated_note']) : null,
        ]);

        return response()->json([
            'success' => true,
            'row' => $this->presentUser($userRh->fresh(), $current),
        ]);
    }

    public function destroy(UserRh $userRh)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $this->guardNonStaff();

        $current = $this->currentUser();
        $this->ensureCanManage($current, $userRh);

        $userRh->delete();

        return response()->json([
            'success' => true,
            'message' => __('messages.deleted'),
        ]);
    }
}

