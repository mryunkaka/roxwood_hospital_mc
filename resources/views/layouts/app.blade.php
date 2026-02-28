<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="theme-light"
      x-data="{
          ...themeController(),
          ...accessibilityController()
      }"
      :class="{
          'theme-dark': theme === 'dark' || (theme === 'stylis' && isDark),
          'theme-stylis': theme === 'stylis',
          'high-contrast': highContrast,
          'reduced-motion': reducedMotion
      }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">

    <title>@yield('title', __('messages.app_name'))</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Vite CSS --}}
    @vite(['resources/css/app.css'])

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Custom Styles --}}
    <style>
        :root {
            --font-sans: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }
        body {
            font-family: var(--font-sans);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-background text-text-primary antialiased min-h-screen"
      :class="{ 'overflow-hidden': sidebarOpen && !sidebarHidden && window.innerWidth < 1024 }">

    @php
        use App\Models\UserRh;
        use Carbon\Carbon;
        use Illuminate\Support\Facades\Storage;
        use Illuminate\Support\Str;

        $authUserUi = null;
        $sessionUser = session('user', []);
        $authUser = null;

        if (!empty($sessionUser['id'])) {
            $authUser = UserRh::find($sessionUser['id']);
        }

        $fullName = $authUser?->full_name ?? ($sessionUser['name'] ?? '');
        $batch = $authUser?->batch ?? ($sessionUser['batch'] ?? null);
        $position = $authUser?->position ?? ($sessionUser['position'] ?? null);
        $role = $authUser?->role ?? ($sessionUser['role'] ?? null);
        $gender = $authUser?->jenis_kelamin;
        $photoProfile = $authUser?->photo_profile ?? ($sessionUser['photo'] ?? null);

        $photoUrl = null;
        if (!empty($photoProfile)) {
            $photoUrl = Str::startsWith($photoProfile, ['http://', 'https://'])
                ? $photoProfile
                : asset($photoProfile);
        } else {
            $genderKey = ($gender === 'Perempuan') ? 'Female' : 'Male';
            $positionNorm = Str::of((string) $position)->lower()->replace([' ', '_'], '-')->toString();
            $roleNorm = Str::of((string) $role)->lower()->replace([' ', '_'], '-')->toString();

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

            $candidate = "logo_profile/{$prefix}-{$genderKey}.png";
            if (!Storage::disk('public')->exists($candidate)) {
                $candidate = "logo_profile/Trainee-{$genderKey}.png";
            }

            $photoUrl = asset('storage/' . $candidate);
        }

        $initial = Str::of($fullName)->trim()->substr(0, 1)->upper()->toString();

        $joinedAt = $authUser?->tanggal_masuk ?? $authUser?->created_at;

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

        $authUserUi = [
            'full_name' => $fullName,
            'initial' => $initial ?: 'A',
            'batch' => $batch,
            'position' => $position,
            'role' => $role,
            'photo_url' => $photoUrl,
            'join_date_id' => $joinDateId,
            'join_date_en' => $joinDateEn,
            'tenure_id' => $tenureId,
            'tenure_en' => $tenureEn,
        ];
    @endphp

    {{-- App Container --}}
    <div class="flex h-screen overflow-hidden bg-pattern">

        {{-- Sidebar --}}
        @include('layouts.sidebar', ['authUserUi' => $authUserUi])

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Navbar --}}
            @include('layouts.navbar', ['authUserUi' => $authUserUi])

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>

        </div>

    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen && !sidebarHidden && window.innerWidth < 1024"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
         @click="closeSidebar()"
         x-cloak></div>

    {{-- Toast Container --}}
    <x-toast />

    {{-- Forced Logout / Session Invalid Modal --}}
    <div
        x-data
        x-show="$store.sessionGuard?.open"
        x-transition
        x-cloak
        class="fixed inset-0 z-[9998] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        @keydown.escape.window="$store.sessionGuard?.acknowledge()"
    >
        <div class="w-full max-w-md rounded-2xl bg-surface border border-border shadow-xl p-6">
            <h3 class="text-lg font-semibold text-text-primary mb-2"
                x-show="$store.sessionGuard?.reason === 'force_offline'"
                data-translate="forced_logout_title">
                {{ __('messages.forced_logout_title') }}
            </h3>
            <h3 class="text-lg font-semibold text-text-primary mb-2"
                x-show="$store.sessionGuard?.reason !== 'force_offline'"
                data-translate="session_invalid_title">
                {{ __('messages.session_invalid_title') }}
            </h3>

            <p class="text-sm text-text-secondary mb-4"
               x-show="$store.sessionGuard?.reason === 'force_offline'">
                <span data-translate="forced_logout_message">{{ __('messages.forced_logout_message') }}</span>
                <template x-if="$store.sessionGuard?.forcedByDevice">
                    <span class="block mt-2 text-xs text-text-tertiary">
                        <span data-translate="forced_logout_by_device">{{ __('messages.forced_logout_by_device') }}</span>:
                        <span class="font-medium" x-text="$store.sessionGuard.forcedByDevice"></span>
                    </span>
                </template>
            </p>

            <p class="text-sm text-text-secondary mb-4"
               x-show="$store.sessionGuard?.reason !== 'force_offline'"
               data-translate="session_invalid_message">
                {{ __('messages.session_invalid_message') }}
            </p>

            <div class="flex justify-end gap-3">
                <button type="button"
                        class="px-4 py-2 rounded-xl bg-primary text-white hover:bg-primary-600 transition-colors"
                        @click="$store.sessionGuard?.acknowledge()">
                    <span data-translate="ok">{{ __('messages.ok') }}</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Vite JS --}}
    @vite(['resources/js/app.js'])

    @stack('scripts')
</body>
</html>
