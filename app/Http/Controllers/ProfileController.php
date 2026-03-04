<?php

namespace App\Http\Controllers;

use App\Models\UserRh;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display profile page.
     */
    public function index()
    {
        $locale = Session::get('locale', 'id');
        app()->setLocale($locale);

        $userId = (int) session('user.id', 0);
        $userRh = $userId > 0 ? UserRh::query()->find($userId) : null;

        $profileUi = null;
        if ($userRh) {
            $fullName = (string) ($userRh->full_name ?? '');
            $position = (string) ($userRh->position ?? '');
            $role = (string) ($userRh->role ?? '');
            $gender = (string) ($userRh->jenis_kelamin ?? '');
            $batch = $userRh->batch;

            $photoProfile = (string) ($userRh->photo_profile ?? '');
            $photoUrl = null;
            if ($photoProfile !== '') {
                $photoUrl = Str::startsWith($photoProfile, ['http://', 'https://'])
                    ? $photoProfile
                    : asset($photoProfile);
            } else {
                $genderKey = ($gender === 'Perempuan') ? 'Female' : 'Male';
                $positionNorm = Str::of($position)->lower()->replace([' ', '_'], '-')->toString();
                $roleNorm = Str::of($role)->lower()->replace([' ', '_'], '-')->toString();

                $prefix = null;
                if (str_contains($roleNorm, 'director')) {
                    $prefix = 'Director';
                } elseif (str_contains($roleNorm, 'manager')) {
                    $prefix = 'Manager';
                }

                if ($prefix === null && $positionNorm !== '') {
                    if (str_contains($positionNorm, 'co.ast') || str_contains($positionNorm, 'co-ast') || str_contains($positionNorm, 'coast')) {
                        $prefix = 'Co.Ast';
                    } elseif (
                        (str_contains($positionNorm, 'specialist') && str_contains($positionNorm, 'doctor')) ||
                        (str_contains($positionNorm, 'dokter') && str_contains($positionNorm, 'spesialis')) ||
                        (str_contains($positionNorm, 'spesialist') && str_contains($positionNorm, 'dokter'))
                    ) {
                        $prefix = 'Specialist-Doctor';
                    } elseif (str_contains($positionNorm, 'doctor') || str_contains($positionNorm, 'dokter')) {
                        $prefix = 'Doctor';
                    } elseif (str_contains($positionNorm, 'paramedic')) {
                        $prefix = 'Paramedic';
                    } elseif (str_contains($positionNorm, 'trainee')) {
                        $prefix = 'Trainee';
                    } elseif (str_contains($positionNorm, 'manager')) {
                        $prefix = 'Manager';
                    }
                }

                if ($prefix === null && $roleNorm !== '') {
                    if (str_contains($roleNorm, 'director')) {
                        $prefix = 'Director';
                    } elseif (str_contains($roleNorm, 'manager')) {
                        $prefix = 'Manager';
                    } elseif (str_contains($roleNorm, 'staff')) {
                        $prefix = 'Trainee';
                    }
                }

                $prefix ??= 'Trainee';

                $photoUrl = route('api.assets.logo_profile', [
                    'filename' => "{$prefix}-{$genderKey}.png",
                ]);
            }

            $initial = Str::of($fullName)->trim()->substr(0, 1)->upper()->toString();

            $joinedAt = $userRh->tanggal_masuk ?? $userRh->created_at;
            $joinDateId = $joinedAt instanceof Carbon ? $joinedAt->copy()->locale('id')->translatedFormat('d M Y') : null;
            $joinDateEn = $joinedAt instanceof Carbon ? $joinedAt->copy()->locale('en')->translatedFormat('d M Y') : null;

            $tenureId = null;
            $tenureEn = null;
            if ($joinedAt instanceof Carbon) {
                $diff = $joinedAt->copy()->startOfDay()->diff(now()->startOfDay());
                $totalMonths = ($diff->y * 12) + $diff->m;

                if ($totalMonths > 0) {
                    $days = $diff->d;
                    $tenureId = trim(($totalMonths ? "{$totalMonths} bulan" : '') . ($days ? " {$days} hari" : '')) ?: '0 hari';
                    $tenureEn = trim(($totalMonths ? "{$totalMonths} month" . ($totalMonths > 1 ? 's' : '') : '') . ($days ? " {$days} day" . ($days > 1 ? 's' : '') : '')) ?: '0 days';
                } else {
                    $weeks = intdiv((int) $diff->days, 7);
                    $days = (int) $diff->days % 7;
                    $tenureId = trim(($weeks ? "{$weeks} minggu" : '') . ($days ? " {$days} hari" : '')) ?: '0 hari';
                    $tenureEn = trim(($weeks ? "{$weeks} week" . ($weeks > 1 ? 's' : '') : '') . ($days ? " {$days} day" . ($days > 1 ? 's' : '') : '')) ?: '0 days';
                }
            }

            $profileUi = [
                'full_name' => $fullName,
                'initial' => $initial ?: 'A',
                'batch' => $batch,
                'position' => $position ?: null,
                'role' => $role ?: null,
                'photo_url' => $photoUrl,
                'join_date_id' => $joinDateId,
                'join_date_en' => $joinDateEn,
                'tenure_id' => $tenureId,
                'tenure_en' => $tenureEn,
            ];
        }

        return view('pages.profile.index', [
            'userRh' => $userRh,
            'profileUi' => $profileUi,
        ]);
    }
}
