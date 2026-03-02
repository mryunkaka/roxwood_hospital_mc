<?php

namespace App\Http\Controllers;

use App\Models\UserRh;
use App\Models\UserFarmasiSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Intervention\Image\ImageManagerStatic as Image;

class AuthController extends Controller
{
    private function avatarVariant(?string $position, ?string $role): string
    {
        $positionNorm = Str::of((string) $position)->lower()->replace([' ', '_'], '-')->toString();
        $roleNorm = Str::of((string) $role)->lower()->replace([' ', '_'], '-')->toString();

        // Role-based variant must win over position (e.g., Director who is also a doctor).
        if (str_contains($roleNorm, 'director')) {
            return 'director';
        }
        if (str_contains($roleNorm, 'manager')) {
            return 'manager';
        }

        if ($positionNorm !== '') {
            if (str_contains($positionNorm, 'trainee')) {
                return 'trainee';
            }
            if (str_contains($positionNorm, 'paramedic')) {
                return 'paramedic';
            }
            if (str_contains($positionNorm, 'co.ast') || str_contains($positionNorm, 'co-ast') || str_contains($positionNorm, 'coast')) {
                return 'coast';
            }
            if (
                (str_contains($positionNorm, 'specialist') && str_contains($positionNorm, 'doctor')) ||
                (str_contains($positionNorm, 'dokter') && str_contains($positionNorm, 'spesialis')) ||
                (str_contains($positionNorm, 'spesialist') && str_contains($positionNorm, 'dokter'))
            ) {
                return 'specialist-doctor';
            }
            if (str_contains($positionNorm, 'doctor') || str_contains($positionNorm, 'dokter')) {
                return 'doctor';
            }
            if (str_contains($positionNorm, 'manager')) {
                return 'manager';
            }
        }

        return 'trainee';
    }

    private function defaultProfilePhotoUrl(?string $position, ?string $role, ?string $jenisKelamin): string
    {
        $genderKey = ($jenisKelamin === 'Perempuan') ? 'Female' : 'Male';

        $positionNorm = Str::of((string) $position)->lower()->replace([' ', '_'], '-')->toString();
        $roleNorm = Str::of((string) $role)->lower()->replace([' ', '_'], '-')->toString();

        $prefix = null;

        // If permission role is Director/Vice Director, force Director avatar.
        if (str_contains($roleNorm, 'director')) {
            $prefix = 'Director';
        } elseif (str_contains($roleNorm, 'manager')) {
            // Staff Manager / Lead Manager / Head Manager / Manager
            $prefix = 'Manager';
        }

        // Prefer job position first (medical role), then permission role as fallback.
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

        return asset('storage/' . $candidate);
    }

    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        // Jika sudah login dan session masih valid, langsung ke dashboard
        $userId = Session::get('user.id');
        $sessionId = Session::get('farmasi_session_id');
        $expiresAt = Session::get('expires_at');
        if ($userId && $sessionId && $expiresAt) {
            try {
                $expiryDate = \Carbon\Carbon::parse($expiresAt);
                if ($expiryDate->isFuture()) {
                    $exists = UserFarmasiSession::where('id', $sessionId)
                        ->where('user_id', $userId)
                        ->whereNull('session_end')
                        ->exists();
                    if ($exists) {
                        return redirect()->route('dashboard');
                    }
                }
            } catch (\Throwable $e) {
                // ignore parse errors
            }
        }

        // Check if user has saved credentials (remember me)
        $savedFullName = Session::get('remember_full_name');
        $savedPin = Session::get('remember_pin');

        $deviceId = request()->cookie('rh_device_id');
        if (!$deviceId) {
            $deviceId = (string) Str::uuid();
        }

        return response()
            ->view('pages.auth.login', [
            'savedFullName' => $savedFullName,
            'savedPin' => $savedPin,
            ])
            ->cookie('rh_device_id', $deviceId, 60 * 24 * 365 * 5);
    }

    /**
     * Search users for autocomplete (min 2 characters)
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q', '');

        // Validate minimum 2 characters
        if (strlen($query) < 2) {
            return response()->json([
                'results' => []
            ]);
        }

        // Search users by full_name
        $users = UserRh::where('full_name', 'like', '%' . $query . '%')
            ->select(['id', 'full_name', 'photo_profile', 'position', 'role', 'batch', 'is_active', 'jenis_kelamin'])
            ->limit(10)
            ->get();

        // Check active sessions for each user
        $results = $users->map(function ($user) {
            // Check if user has active session (session_end is null and within last hour)
            $activeSession = UserFarmasiSession::where('user_id', $user->id)
                ->whereNull('session_end')
                ->where('session_start', '>=', now()->subHours(24))
                ->first();

            // Get photo URL
            $photoUrl = null;
            if (!empty($user->photo_profile)) {
                $photoUrl = Str::startsWith($user->photo_profile, ['http://', 'https://'])
                    ? $user->photo_profile
                    : asset($user->photo_profile);
            } else {
                $photoUrl = $this->defaultProfilePhotoUrl($user->position, $user->role, $user->jenis_kelamin);
            }

            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'photo' => $photoUrl,
                'avatar_variant' => $this->avatarVariant($user->position, $user->role),
                'position' => $user->position ?? 'Trainee',
                'role' => $user->role,
                'batch' => $user->batch,
                'is_active' => $user->is_active,
                'is_online' => $activeSession !== null,
            ];
        });

        return response()->json([
            'results' => $results
        ]);
    }

    /**
     * Get active session info for a user
     */
    public function getActiveSession(Request $request)
    {
        $userId = $request->get('user_id');

        if (!$userId) {
            return response()->json([
                'has_active_session' => false
            ]);
        }

        $activeSession = UserFarmasiSession::where('user_id', $userId)
            ->whereNull('session_end')
            ->where('session_start', '>=', now()->subHours(24))
            ->first();

        return response()->json([
            'has_active_session' => $activeSession !== null,
            'session_info' => $activeSession ? [
                'device_info' => $this->sanitizeSessionInfo($activeSession->medic_jabatan ?? 'Unknown Device'),
                'started_at' => $activeSession->session_start?->format('Y-m-d H:i:s'),
            ] : null,
        ]);
    }

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        $deviceId = request()->cookie('rh_device_id');
        if (!$deviceId) {
            $deviceId = (string) Str::uuid();
        }

        return response()
            ->view('pages.auth.register')
            ->cookie('rh_device_id', $deviceId, 60 * 24 * 365 * 5);
    }

    /**
     * Proses login dengan full_name dan PIN
     */
    public function login(Request $request)
    {
        $locale = Session::get('locale', 'id');
        app()->setLocale($locale);

        // Validate input
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'pin' => 'required|string|size:4|regex:/^[0-9]+$/',
            'remember' => 'nullable',
            'force_login' => 'nullable|boolean',
        ], [
            'full_name.required' => __('messages.full_name') . ' ' . __('messages.required_field'),
            'pin.required' => __('messages.pin') . ' ' . __('messages.required_field'),
            'pin.regex' => __('messages.pin') . ' harus berupa angka',
            'pin.size' => __('messages.pin') . ' harus 4 digit',
        ]);

        // Find user by full_name (case insensitive)
        $user = UserRh::whereRaw('LOWER(full_name) = LOWER(?)', [$validated['full_name']])->first();

        // Check if user exists
        if (!$user) {
            return back()->withErrors([
                'full_name' => __('messages.full_name_not_found'),
            ])->withInput();
        }

        // Verify PIN (check hashed first, then plain text for backward compatibility)
        $pinValid = false;
        if (Hash::check($validated['pin'], $user->pin)) {
            $pinValid = true;
        } elseif ($user->pin === $validated['pin']) {
            // For backward compatibility with plain text PINs
            $pinValid = true;
        }

        if (!$pinValid) {
            return back()->withErrors([
                'pin' => __('messages.pin_incorrect'),
            ])->withInput();
        }

        // Check if user is active (validated)
        if (!$user->is_active) {
            return back()->withErrors([
                'full_name' => __('messages.account_not_validated'),
            ])->withInput();
        }

        $forceLogin = (bool) ($validated['force_login'] ?? false);

        $deviceId = $request->cookie('rh_device_id') ?: (string) Str::uuid();

        // Jika ada session aktif dan user belum memilih "Paksa Login" -> tampilkan modal konfirmasi
        $existingSession = UserFarmasiSession::where('user_id', $user->id)
            ->whereNull('session_end')
            ->where('session_start', '>=', now()->subHours(24))
            ->first();

        // Jika session aktif itu dari device yang sama (browser yang sama), jangan munculkan modal.
        // Karena table session bisa dihapus, kita simpan mapping session->device di cache.
        $existingSessionDeviceId = null;
        if ($existingSession) {
            $existingSessionDeviceId = Cache::get('farmasi_session_device:' . $existingSession->id);
            if (!$existingSessionDeviceId) {
                $existingSessionDeviceId = $this->extractDeviceIdFromSessionInfo($existingSession->medic_jabatan);
            }
            if (!$forceLogin && $existingSessionDeviceId && hash_equals((string) $existingSessionDeviceId, (string) $deviceId)) {
                $forceLogin = true;
            }

            // Fallback untuk session lama (sebelum ada device_id di cache):
            // jika signature device sama, anggap ini device yang sama (tanpa "Paksa Login").
            if (!$forceLogin && !$existingSessionDeviceId) {
                $sig = $this->getDeviceSignature();
                $activeInfo = (string) ($existingSession->medic_jabatan ?? '');
                if ($sig !== '' && str_starts_with($activeInfo, $sig)) {
                    $forceLogin = true;
                }
            }
        }

        if ($existingSession && !$forceLogin) {
            $activeDevice = $this->sanitizeSessionInfo($existingSession->medic_jabatan ?? 'Unknown Device');
            if (!empty($existingSessionDeviceId)) {
                $activeDevice .= ' · ID:' . substr((string) $existingSessionDeviceId, -6);
            }
            return back()
                ->with([
                    'confirm_force_login' => true,
                    'active_device' => $activeDevice,
                    'full_name' => $validated['full_name'],
                    'pin' => $validated['pin'],
                    'remember' => (bool) ($validated['remember'] ?? false),
                ])
                ->withInput();
        }

        // Check for active session on another device (force login path)
        $deviceInfo = $this->getDeviceInfo();
        DB::transaction(function () use ($user, $deviceInfo, $deviceId) {
            $sig = $this->getDeviceSignature();

            $activeSessions = UserFarmasiSession::where('user_id', $user->id)
                ->whereNull('session_end')
                ->where('session_start', '>=', now()->subHours(24))
                ->get();

            if ($activeSessions->isNotEmpty()) {
                $endAt = now();
                foreach ($activeSessions as $session) {
                    $sessionDeviceId = Cache::get('farmasi_session_device:' . $session->id);
                    if (!$sessionDeviceId) {
                        $sessionDeviceId = $this->extractDeviceIdFromSessionInfo($session->medic_jabatan);
                    }
                    $isSameDevice = false;
                    if ($sessionDeviceId && hash_equals((string) $sessionDeviceId, (string) $deviceId)) {
                        $isSameDevice = true;
                    } elseif (!$sessionDeviceId && $sig !== '' && str_starts_with((string) ($session->medic_jabatan ?? ''), $sig)) {
                        // Legacy session fallback
                        $isSameDevice = true;
                    }

                    // Simpan info untuk notifikasi device lain (karena row akan dihapus)
                    // Jangan set forced logout jika session yang digantikan berasal dari device yang sama.
                    if (!$isSameDevice) {
                        Cache::put(
                            'forced_logout:' . $session->id,
                            [
                                'forced_by_device' => $deviceInfo,
                                'ended_at' => $endAt->toIso8601String(),
                            ],
                            now()->addDay()
                        );
                    }

                    $session->update([
                        'session_end' => $endAt,
                        'duration_seconds' => abs((int) $endAt->diffInSeconds($session->session_start, false)),
                        'end_reason' => $isSameDevice ? 'auto_offline' : 'force_offline',
                        'ended_by_user_id' => $user->id,
                    ]);
                    // User request: hapus session sebelumnya dari database
                    $session->delete();

                    Cache::forget('farmasi_session_device:' . $session->id);
                }
            }

            // Create new session record
            $newSession = UserFarmasiSession::create([
                'user_id' => $user->id,
                'medic_name' => $user->full_name,
                'medic_jabatan' => $this->getDeviceInfoForSession($deviceId),
                'session_start' => now(),
            ]);

            Session::put('farmasi_session_id', $newSession->id);

            Cache::put('farmasi_session_device:' . $newSession->id, $deviceId, now()->addDays(7));
        });

        // Set session with 1 year expiration
        $sessionData = [
            'user' => [
                'id' => $user->id,
                'name' => $user->full_name,
                'role' => $user->role,
                'citizen_id' => $user->citizen_id,
                'batch' => $user->batch,
                'position' => $user->position ?? 'Trainee',
                'photo' => $user->photo_profile,
            ],
            'logged_in_at' => now()->toIso8601String(),
            'expires_at' => now()->addYear()->toIso8601String(),
        ];

        Session::put($sessionData);

        // Remember me - save credentials to session
        if ($validated['remember'] ?? false) {
            Session::put('remember_full_name', $validated['full_name']);
            Session::put('remember_pin', $validated['pin']);
        } else {
            Session::forget('remember_full_name');
            Session::forget('remember_pin');
        }

        // Redirect to intended page or dashboard
        return redirect()
            ->intended(route('dashboard'))
            ->cookie('rh_device_id', $deviceId, 60 * 24 * 365 * 5);
    }

    /**
     * Get device information from request
     */
    private function getDeviceSignature(): string
    {
        $userAgent = request()->userAgent();
        $deviceType = 'Unknown';

        if (strpos($userAgent, 'Mobile') !== false || strpos($userAgent, 'Android') !== false || strpos($userAgent, 'iPhone') !== false) {
            $deviceType = 'Mobile Device';
        } elseif (strpos($userAgent, 'Tablet') !== false || strpos($userAgent, 'iPad') !== false) {
            $deviceType = 'Tablet';
        } elseif (strpos($userAgent, 'Windows') !== false) {
            $deviceType = 'Windows PC';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            $deviceType = 'Mac';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $deviceType = 'Linux PC';
        }

        // Add browser info
        $browser = 'Unknown Browser';
        // Order matters: Edge/Opera UAs include "Chrome"
        if (strpos($userAgent, 'Edg') !== false || strpos($userAgent, 'Edge') !== false) {
            $browser = 'Edge';
        } elseif (strpos($userAgent, 'OPR') !== false || strpos($userAgent, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Safari';
        }

        return $deviceType . ' - ' . $browser;
    }

    private function getDeviceInfo(): string
    {
        return $this->getDeviceSignature() . ' (' . now()->format('Y-m-d H:i') . ')';
    }

    private function getDeviceInfoForSession(string $deviceId): string
    {
        // Persist device_id in DB without new column
        return $this->getDeviceInfo() . ' [did:' . $deviceId . ']';
    }

    private function extractDeviceIdFromSessionInfo(?string $info): ?string
    {
        if (!$info) return null;
        if (preg_match('/\\[did:([a-f0-9\\-]{10,})\\]/i', $info, $m)) {
            return (string) $m[1];
        }
        return null;
    }

    private function sanitizeSessionInfo(?string $info): string
    {
        $value = (string) ($info ?? '');
        return trim((string) preg_replace('/\\s*\\[did:[^\\]]+\\]\\s*/i', ' ', $value));
    }

    /**
     * Proses register dengan file storage, image compression, dan PDF generation
     */
    public function register(Request $request)
    {
        // Validate input
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:100',
                'pin' => 'required|string|size:4|regex:/^[0-9]+$/',
                'batch' => 'required|integer|min:1|max:26',
                'citizen_id' => 'required|string|max:30|unique:user_rh,citizen_id',
                'no_hp_ic' => 'required|string|max:20',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tanggal_masuk' => 'required|date',
                'photo_profile' => 'nullable|file|mimes:png,jpeg,jpg',
                'file_ktp' => 'required|file|mimes:png,jpeg,jpg|max:5120',
                'file_skb' => 'required|file|mimes:png,jpeg,jpg|max:5120',
                'file_sim' => 'nullable|file|mimes:png,jpeg,jpg|max:5120',
                'role' => 'required|in:Staff,Staff Manager,Lead Manager,Head Manager,Vice Director,Director',
                'signature_data' => 'nullable|string',
                'signature_file' => 'nullable|file|mimes:png,jpeg,jpg|max:5120',
                'terms' => 'accepted',
            ], [
                'full_name.required' => __('messages.full_name') . ' ' . __('messages.required_field'),
                'pin.required' => __('messages.pin') . ' ' . __('messages.required_field'),
                'pin.regex' => __('messages.pin') . ' harus berupa angka',
                'batch.required' => __('messages.batch') . ' ' . __('messages.required_field'),
                'citizen_id.required' => __('messages.citizen_id') . ' ' . __('messages.required_field'),
                'citizen_id.unique' => __('messages.citizen_id') . ' sudah terdaftar',
                'file_ktp.required' => __('messages.ktp_file') . ' ' . __('messages.required_field'),
                'file_skb.required' => __('messages.skb_file') . ' ' . __('messages.required_field'),
                'terms.accepted' => __('messages.agree_terms') . ' ' . __('messages.required_field'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        }

        // Get current language and set it for translations
        $locale = Session::get('locale', 'id');
        $isIndonesian = $locale === 'id';
        app()->setLocale($locale);

        // Format input data
        // Full Name: Convert to Title Case (MiChAel MooRe -> Michael Moore)
        $validated['full_name'] = ucwords(strtolower($validated['full_name']));

        // Citizen ID: Convert to UPPERCASE (JhsjS212 -> JHSJS212)
        $validated['citizen_id'] = strtoupper($validated['citizen_id']);

        // Cek nama duplikat (gunakan nama yang sudah diformat)
        if (UserRh::where('full_name', $validated['full_name'])->exists()) {
            return back()->withErrors(['full_name' => 'Nama sudah terdaftar'])->withInput();
        }

        // Generate API token
        try {
            $apiToken = bin2hex(random_bytes(32));
        } catch (\Exception $e) {
            $apiToken = bin2hex(openssl_random_pseudo_bytes(32));
        }

        // Generate kode nomor induk RS
        try {
            $batchCode = chr(64 + $validated['batch']); // 1 = A, 2 = B, etc.
            $randomNum = str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT);
            $nameInitials = strtoupper(substr($validated['full_name'], 0, 3));
            $kodeNomorInduk = 'RH' . $batchCode . $randomNum . $nameInitials;
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal generate kode nomor induk: ' . $e->getMessage()])->withInput();
        }

        // Default position
        $position = 'Trainee';

        // Create user record FIRST (tanpa file paths) untuk mendapatkan ID
        try {
            $user = UserRh::create([
                'full_name' => $validated['full_name'],
                'pin' => Hash::make($validated['pin']), // Hash the PIN
                'citizen_id' => $validated['citizen_id'],
                'no_hp_ic' => $validated['no_hp_ic'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'batch' => $validated['batch'],
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'role' => $validated['role'],
                'position' => $position,
                'kode_nomor_induk_rs' => $kodeNomorInduk,
                'api_token' => $apiToken,
                'is_verified' => false, // Perlu verifikasi
                'is_active' => false,  // Tidak aktif sampai di-verify
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan data user: ' . $e->getMessage()])->withInput();
        }

        // Format nama folder: user_{id}-{nama_lower}-{citizen_id}
        $sanitizedName = strtolower(str_replace(' ', '_', $validated['full_name']));
        $folderName = 'user_' . $user->id . '-' . $sanitizedName . '-' . $validated['citizen_id'];
        $baseDir = public_path('storage/user_docs/');
        $uploadDir = $baseDir . $folderName;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            // Rollback user jika folder gagal dibuat
            $user->delete();
            return back()->withErrors(['error' => 'Gagal membuat folder dokumen'])->withInput();
        }

        // Save uploaded files dengan compression
        $savedFiles = [];

        // Save Profile Photo (optional) with compression
        if ($request->hasFile('photo_profile')) {
            try {
                $photoFile = $request->file('photo_profile');
                $photoFileName = 'profile_photo.jpg';
                $photoPath = $uploadDir . '/' . $photoFileName;
                if (!$this->compressImageSmart($photoFile->getPathname(), $photoPath, 800, 500000)) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal mengompres foto profil'])->withInput();
                }
                $savedFiles['photo_profile'] = 'storage/user_docs/' . $folderName . '/' . $photoFileName;
            } catch (\Exception $e) {
                $user->delete();
                return back()->withErrors(['error' => 'Gagal upload foto profil: ' . $e->getMessage()])->withInput();
            }
        }

        // Save KTP with compression
        if ($request->hasFile('file_ktp')) {
            try {
                $ktpFile = $request->file('file_ktp');
                $ktpFileName = 'file_ktp.jpg';
                $ktpPath = $uploadDir . '/' . $ktpFileName;
                if (!$this->compressImageSmart($ktpFile->getPathname(), $ktpPath)) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal mengompres file KTP'])->withInput();
                }
                $savedFiles['ktp'] = 'storage/user_docs/' . $folderName . '/' . $ktpFileName;
            } catch (\Exception $e) {
                $user->delete();
                return back()->withErrors(['error' => 'Gagal upload file KTP: ' . $e->getMessage()])->withInput();
            }
        }

        // Save SKB with compression
        if ($request->hasFile('file_skb')) {
            try {
                $skbFile = $request->file('file_skb');
                $skbFileName = 'file_skb.jpg';
                $skbPath = $uploadDir . '/' . $skbFileName;
                if (!$this->compressImageSmart($skbFile->getPathname(), $skbPath)) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal mengompres file SKB'])->withInput();
                }
                $savedFiles['skb'] = 'storage/user_docs/' . $folderName . '/' . $skbFileName;
            } catch (\Exception $e) {
                $user->delete();
                return back()->withErrors(['error' => 'Gagal upload file SKB: ' . $e->getMessage()])->withInput();
            }
        }

        // Save SIM (optional) with compression
        if ($request->hasFile('file_sim')) {
            try {
                $simFile = $request->file('file_sim');
                $simFileName = 'file_sim.jpg';
                $simPath = $uploadDir . '/' . $simFileName;
                if (!$this->compressImageSmart($simFile->getPathname(), $simPath)) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal mengompres file SIM'])->withInput();
                }
                $savedFiles['sim'] = 'storage/user_docs/' . $folderName . '/' . $simFileName;
            } catch (\Exception $e) {
                $user->delete();
                return back()->withErrors(['error' => 'Gagal upload file SIM: ' . $e->getMessage()])->withInput();
            }
        }

        // Save Signature (required) - Digital or Upload
        try {
            // Check if digital signature data is provided
            if (!empty($validated['signature_data'])) {
                // Digital signature from canvas (base64)
                $signatureData = $validated['signature_data'];

                // Remove data:image/png;base64, prefix if present
                if (strpos($signatureData, 'data:image/png;base64,') === 0) {
                    $signatureData = substr($signatureData, 22);
                }

                // Decode base64 and save with proper transparency
                $signatureImage = imagecreatefromstring(base64_decode($signatureData));
                if (!$signatureImage) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal memproses tanda tangan digital'])->withInput();
                }

                // Convert to true color and enable transparency
                imagepalettetotruecolor($signatureImage);
                imagealphablending($signatureImage, false);
                imagesavealpha($signatureImage, true);

                $signatureFileName = 'signature.png';
                $signaturePath = $uploadDir . '/' . $signatureFileName;
                imagepng($signatureImage, $signaturePath, 9);
                imagedestroy($signatureImage);

                $savedFiles['signature'] = 'storage/user_docs/' . $folderName . '/' . $signatureFileName;

            } elseif ($request->hasFile('signature_file')) {
                // Uploaded signature file - need to remove background
                $signatureFile = $request->file('signature_file');
                $tempPath = $signatureFile->getPathname();
                $signatureFileName = 'signature.png';
                $signaturePath = $uploadDir . '/' . $signatureFileName;

                // Process signature: remove background and save as PNG
                if (!$this->processSignatureImage($tempPath, $signaturePath)) {
                    $user->delete();
                    return back()->withErrors(['error' => 'Gagal memproses file tanda tangan'])->withInput();
                }

                $savedFiles['signature'] = 'storage/user_docs/' . $folderName . '/' . $signatureFileName;

            } else {
                // No signature provided
                $user->delete();
                return back()->withErrors(['signature' => __('messages.signature_required')])->withInput();
            }
        } catch (\Exception $e) {
            $user->delete();
            return back()->withErrors(['error' => 'Gagal menyimpan tanda tangan: ' . $e->getMessage()])->withInput();
        }

        // Generate PDF Agreement Letter
        try {
            $pdfData = $this->generateAgreementPDF($validated, $savedFiles, $isIndonesian, $locale);
            $pdfFileName = 'agreement_letter.pdf';
            $pdfPath = $uploadDir . '/' . $pdfFileName;
            file_put_contents($pdfPath, $pdfData);
            $savedFiles['pdf'] = 'storage/user_docs/' . $folderName . '/' . $pdfFileName;
        } catch (\Exception $e) {
            $user->delete();
            return back()->withErrors(['error' => 'Gagal generate PDF: ' . $e->getMessage()])->withInput();
        }

        // Update user dengan file paths
        try {
            $user->update([
                'photo_profile' => $savedFiles['photo_profile'] ?? null,
                'file_ktp' => $savedFiles['ktp'] ?? null,
                'file_skb' => $savedFiles['skb'] ?? null,
                'file_sim' => $savedFiles['sim'] ?? null,
                'signature' => $savedFiles['signature'] ?? null,
            ]);
        } catch (\Exception $e) {
            $user->delete();
            return back()->withErrors(['error' => 'Gagal update file paths: ' . $e->getMessage()])->withInput();
        }

        // Clear any existing session
        Session::forget('user');

        // Redirect ke login dengan success message
        return redirect()->route('login')
            ->with('success', __('messages.register_success_message'))
            ->with('success_title', __('messages.register_success_title'));
    }

    /**
     * Generate PDF Agreement Letter
     */
    private function generateAgreementPDF($data, $savedFiles, $isIndonesian, $locale, $signatureBase64Override = null)
    {
        $now = now();

        // Format date based on language
        if ($isIndonesian) {
            $dateFormatter = function($date) {
                $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                $months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                          'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                return $days[$date->format('w')] . ', ' . $date->format('j') . ' ' .
                       $months[$date->format('n') - 1] . ' ' . $date->format('Y');
            };
        } else {
            $dateFormatter = function($date) {
                return $date->format('l, F j, Y');
            };
        }

        $formattedDate = $dateFormatter($now);
        $formattedTime = $now->format('H:i');
        $formattedDateTime = $formattedDate . ', ' . $formattedTime;

        // Convert logo to base64 for reliable PDF rendering
        $logoPath = public_path('storage/logo rh copy.png');
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));

        // Convert signature to base64 for reliable PDF rendering
        $signatureBase64 = null;
        // Use override signature if provided (for preview)
        if ($signatureBase64Override) {
            $signatureBase64 = $signatureBase64Override;
        } elseif (isset($savedFiles['signature'])) {
            $signatureFullPath = public_path($savedFiles['signature']);
            if (file_exists($signatureFullPath)) {
                $signatureBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($signatureFullPath));
            }
        }

        // Prepare data for PDF view
        $pdfData = [
            'isIndonesian' => $isIndonesian,
            'locale' => $locale,
            'fullName' => $data['full_name'],
            'citizenId' => $data['citizen_id'],
            'batch' => $data['batch'],
            'formattedDate' => $formattedDate,
            'formattedTime' => $formattedTime,
            'formattedDateTime' => $formattedDateTime,
            'logoPath' => $logoBase64,
            'signaturePath' => $signatureBase64,
            'savedFiles' => $savedFiles,
        ];

        // Load translations
        if ($isIndonesian) {
            $pdfData = array_merge($pdfData, [
                'title' => 'Pernyataan Persetujuan dan Komitmen',
                'subtitle' => 'Persetujuan Kerja di Roxwood Health Medical Center',
                'hospitalName' => 'Roxwood Health Medical Center',
                'preamble' => "Pada hari ini $formattedDate, pada jam $formattedTime, saya yang bertanda tangan di bawah ini:",
                'firstPartyLabel' => 'Pihak Pertama:',
                'firstPartyDesc' => 'Manajemen Rumah Sakit Roxwood Health Medical Center',
                'secondPartyLabel' => 'Pihak Kedua:',
                'secondPartyLabelName' => 'Nama',
                'citizenIdLabel' => 'ID Kewarganegaraan',
                'batchLabel' => 'Batch',
                'agreementText' => 'Pihak Pertama telah memberikan poin-poin penting untuk Pihak Kedua, dan dengan ini Pihak Kedua menyatakan hal-hal sebagai berikut:',
                'point1' => 'Pihak Pertama menetapkan dan Pihak Kedua bersedia mematuhi dan melaksanakan semua Standar Operasional Prosedur (SOP) dan peraturan yang berlaku di Roxwood Health Medical Center, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.',
                'point2' => 'Pihak Pertama menginformasikan dan Pihak Kedua memahami bahwa pelanggaran terhadap SOP dan peraturan yang berlaku akan dikenakan sanksi sesuai dengan ketentuan yang tercantum dalam kontrak kerja yang terdapat dalam In Character (IC), dan Pihak Kedua dengan ini menyatakan sampai seterusnya.',
                'point3' => 'Pihak Pertama menegaskan dan Pihak Kedua bertanggung jawab penuh dan bersedia menerima semua konsekuensi jika terbukti bersalah melakukan pelanggaran, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.',
                'point4' => 'Pihak Pertama mengatur dan Pihak Kedua menyetujui bahwa segala bonus atau gaji sesuai kontrak kerja akan ditahan dan dikembalikan ke rumah sakit, bukan kepada pihak manajemen perseorangan, dan Pihak Kedua dengan ini menyatakan sampai seterusnya.',
                'statement' => 'Pernyataan ini dibuat oleh Pihak Pertama dan Pihak Kedua dengan kesadaran penuh dan tanpa paksaan dari pihak manapun.',
                'firstPartySignature' => 'Pihak Pertama,',
                'management' => 'Manajemen Roxwood Health Medical Center',
                'secondPartySignature' => 'Pihak Kedua,',
                'employee' => 'Pegawai/User',
                'dateTimeLabel' => 'Tanggal & Waktu',
            ]);
        } else {
            $pdfData = array_merge($pdfData, [
                'title' => 'Statement of Agreement and Commitment',
                'subtitle' => 'Employment Agreement at Roxwood Health Medical Center',
                'hospitalName' => 'Roxwood Health Medical Center',
                'preamble' => "On this $formattedDate, at $formattedTime, I the undersigned:",
                'firstPartyLabel' => 'First Party:',
                'firstPartyDesc' => 'Roxwood Health Medical Center Management',
                'secondPartyLabel' => 'Second Party:',
                'secondPartyLabelName' => 'Name',
                'citizenIdLabel' => 'Citizen ID',
                'batchLabel' => 'Batch',
                'agreementText' => 'The First Party has provided important points for the Second Party, and the Second Party hereby states as follows:',
                'point1' => 'The First Party establishes and the Second Party agrees to comply with and implement all Standard Operating Procedures (SOP) and regulations applicable at Roxwood Health Medical Center, and the Second Party hereby declares for all time.',
                'point2' => 'The First Party informs and the Second Party understands that any violation of the applicable SOP and regulations will be subject to sanctions in accordance with the provisions stipulated in the employment contract contained in the In Character (IC), and the Second Party hereby declares for all time.',
                'point3' => 'The First Party emphasizes and the Second Party is fully responsible and willing to accept all consequences if proven guilty of any violation, and the Second Party hereby declares for all time.',
                'point4' => 'The First Party stipulates and the Second Party agrees that any bonuses or salaries in accordance with the employment contract will be withheld and returned to the hospital, not to any individual management party, and the Second Party hereby declares for all time.',
                'statement' => 'This statement is made by the First Party and the Second Party with full awareness and without any coercion from any party.',
                'firstPartySignature' => 'First Party,',
                'management' => 'Roxwood Health Medical Center Management',
                'secondPartySignature' => 'Second Party,',
                'employee' => 'Employee/User',
                'dateTimeLabel' => 'Date & Time',
            ]);
        }

        // Generate PDF
        $pdf = PDF::loadView('pdf.agreement', $pdfData);

        // Set PDF options
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true,
            'chroot' => public_path(), // Allow access to public path
        ]);

        return $pdf->output();
    }

    /**
     * Compress image smart - compress under 300KB while maintaining quality
     */
    private function compressImageSmart(
        string $sourcePath,
        string $targetPath,
        int $maxWidth = 1200,
        int $targetSize = 300000, // 300KB in bytes
        int $minQuality = 65
    ): bool {
        try {
            // Get image info
            $info = getimagesize($sourcePath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'];

            // Load image based on mime type
            if ($mime === 'image/jpeg') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mime === 'image/png') {
                $image = imagecreatefrompng($sourcePath);
            } else {
                return false;
            }

            if (!$image) {
                return false;
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate new dimensions if width exceeds maxWidth
            if ($width > $maxWidth) {
                $ratio = $maxWidth / $width;
                $newWidth = $maxWidth;
                $newHeight = $height * $ratio;

                // Create new image
                $newImage = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG
                if ($mime === 'image/png') {
                    imagealphablending($newImage, false);
                    imagesavealpha($newImage, true);
                    $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                    imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                }

                imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $newImage;
            }

            // Try to save with decreasing quality until under target size
            $quality = 85;
            $maxIterations = 10;

            while ($quality >= $minQuality && $maxIterations > 0) {
                // Create temp file
                $tempPath = $targetPath . '.tmp';

                // Save as JPEG (better compression than PNG)
                imagejpeg($image, $tempPath, $quality);

                // Check file size
                $fileSize = filesize($tempPath);

                if ($fileSize <= $targetSize) {
                    // Success - rename temp file to target
                    rename($tempPath, $targetPath);
                    imagedestroy($image);
                    return true;
                }

                // Delete temp file and try again with lower quality
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $quality -= 5;
                $maxIterations--;
            }

            // If we couldn't get under target size, save with minimum quality anyway
            imagejpeg($image, $targetPath, $minQuality);
            imagedestroy($image);

            return true;
        } catch (\Exception $e) {
            // Log error if needed
            return false;
        }
    }

    /**
     * Process signature image - remove background and save as PNG
     */
    private function processSignatureImage(string $sourcePath, string $targetPath): bool
    {
        try {
            // Get image info
            $info = getimagesize($sourcePath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'];

            // Load image based on mime type
            if ($mime === 'image/jpeg') {
                $image = imagecreatefromjpeg($sourcePath);
            } elseif ($mime === 'image/png') {
                $image = imagecreatefrompng($sourcePath);
            } else {
                return false;
            }

            if (!$image) {
                return false;
            }

            // Convert to true color for better processing
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);

            // Get image dimensions
            $width = imagesx($image);
            $height = imagesy($image);

            // Remove background by making white/near-white pixels transparent
            // Only remove pixels that are VERY close to white to preserve signature strokes
            for ($x = 0; $x < $width; $x++) {
                for ($y = 0; $y < $height; $y++) {
                    // Get pixel color
                    $color = imagecolorat($image, $x, $y);
                    $r = ($color >> 16) & 0xFF;
                    $g = ($color >> 8) & 0xFF;
                    $b = ($color & 0xFF);

                    // Check if pixel is pure white or very near-white (background only)
                    // Use more strict threshold - only remove extremely white pixels
                    // This preserves dark/gray signature strokes
                    if ($r > 245 && $g > 245 && $b > 245) {
                        // Make pixel fully transparent
                        imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, 255, 255, 255, 127));
                    }
                }
            }

            // Save as PNG with transparency
            imagepng($image, $targetPath, 9);
            imagedestroy($image);

            return true;
        } catch (\Exception $e) {
            // Log error if needed
            return false;
        }
    }

    /**
     * Helper: Alpha position for generating kode nomor induk
     */
    private function alphaPos($char)
    {
        $char = strtoupper($char);
        if ($char < 'A' || $char > 'Z') return null;
        return ord($char) - 64;
    }

    /**
     * Helper: Two digit format
     */
    private function twoDigit($num)
    {
        return str_pad($num, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        $userId = Session::get('user.id');
        $sessionId = Session::get('farmasi_session_id');

        // User request: hapus session dari database
        if ($userId && $sessionId) {
            UserFarmasiSession::where('id', $sessionId)
                ->where('user_id', $userId)
                ->delete();
            Cache::forget('farmasi_session_device:' . $sessionId);
        }

        // Clear session
        Session::forget('user');
        Session::forget('logged_in_at');
        Session::forget('expires_at');
        Session::forget('farmasi_session_id');

        return redirect()->route('login');
    }

    /**
     * Check session validity (for middleware)
     */
    public function checkSession()
    {
        $userId = Session::get('user.id');
        $sessionId = Session::get('farmasi_session_id');
        $expiresAt = Session::get('expires_at');

        if (!$userId || !$sessionId || !$expiresAt) {
            return response()->json(['valid' => false, 'reason' => 'missing'], 401);
        }

        $expiryDate = \Carbon\Carbon::parse($expiresAt);

        if ($expiryDate->isPast()) {
            Session::forget('user');
            Session::forget('logged_in_at');
            Session::forget('expires_at');
            Session::forget('farmasi_session_id');
            return response()->json(['valid' => false, 'reason' => 'expired'], 401);
        }

        $exists = UserFarmasiSession::where('id', $sessionId)
            ->where('user_id', $userId)
            ->whereNull('session_end')
            ->exists();

        if (!$exists) {
            // Session sudah dihapus/diakhiri karena login dari device lain
            $forced = Cache::pull('forced_logout:' . $sessionId);
            $reason = $forced ? 'force_offline' : 'superseded';

            Session::forget('user');
            Session::forget('logged_in_at');
            Session::forget('expires_at');
            Session::forget('farmasi_session_id');
            Cache::forget('farmasi_session_device:' . $sessionId);
            return response()->json([
                'valid' => false,
                'reason' => $reason,
                'forced_by_device' => $forced['forced_by_device'] ?? null,
            ], 401);
        }

        return response()->json(['valid' => true]);
    }

    /**
     * TEMPORARY: Preview PDF in Indonesian (DELETE AFTER TESTING)
     */
    public function previewPdfIndonesian(Request $request)
    {
        // GET: Show the form
        if ($request->isMethod('get')) {
            return view('pages.auth.preview-pdf');
        }

        // POST: Process form and generate PDF
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'batch' => 'required|integer|min:1|max:26',
            'citizen_id' => 'required|string|max:30',
            'signature_data' => 'required|string',
        ], [
            'full_name.required' => __('messages.full_name') . ' ' . __('messages.required_field'),
            'batch.required' => __('messages.batch') . ' ' . __('messages.required_field'),
            'citizen_id.required' => __('messages.citizen_id') . ' ' . __('messages.required_field'),
            'signature_data.required' => __('messages.signature_required'),
        ]);

        // Format input data
        $validated['full_name'] = ucwords(strtolower($validated['full_name']));
        $validated['citizen_id'] = strtoupper($validated['citizen_id']);

        // Process signature data
        $signatureBase64 = null;
        if (!empty($validated['signature_data'])) {
            // Remove data:image/png;base64, prefix if present
            $signatureData = $validated['signature_data'];
            if (strpos($signatureData, 'data:image/png;base64,') === 0) {
                $signatureData = substr($signatureData, 22);
            }
            $signatureBase64 = 'data:image/png;base64,' . $signatureData;
        }

        $savedFiles = [];
        $isIndonesian = true;
        $locale = 'id';

        // Set locale for translations
        app()->setLocale($locale);

        // Create a temporary data structure with signature
        $data = [
            'full_name' => $validated['full_name'],
            'citizen_id' => $validated['citizen_id'],
            'batch' => $validated['batch'],
            'signature_base64' => $signatureBase64,
        ];

        // Generate PDF with signature
        $pdfData = $this->generateAgreementPDF($data, $savedFiles, $isIndonesian, $locale, $signatureBase64);

        // Return PDF for preview (stream to browser)
        return response()->make($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview-indonesian.pdf"',
        ]);
    }

    /**
     * TEMPORARY: Preview PDF in English (DELETE AFTER TESTING)
     */
    public function previewPdfEnglish(Request $request)
    {
        // GET: Show the form (same as Indonesian)
        if ($request->isMethod('get')) {
            return view('pages.auth.preview-pdf');
        }

        // POST: Process form and generate PDF
        $validated = $request->validate([
            'full_name' => 'required|string|max:100',
            'batch' => 'required|integer|min:1|max:26',
            'citizen_id' => 'required|string|max:30',
            'signature_data' => 'required|string',
        ], [
            'full_name.required' => __('messages.full_name') . ' ' . __('messages.required_field'),
            'batch.required' => __('messages.batch') . ' ' . __('messages.required_field'),
            'citizen_id.required' => __('messages.citizen_id') . ' ' . __('messages.required_field'),
            'signature_data.required' => __('messages.signature_required'),
        ]);

        // Format input data
        $validated['full_name'] = ucwords(strtolower($validated['full_name']));
        $validated['citizen_id'] = strtoupper($validated['citizen_id']);

        // Process signature data
        $signatureBase64 = null;
        if (!empty($validated['signature_data'])) {
            // Remove data:image/png;base64, prefix if present
            $signatureData = $validated['signature_data'];
            if (strpos($signatureData, 'data:image/png;base64,') === 0) {
                $signatureData = substr($signatureData, 22);
            }
            $signatureBase64 = 'data:image/png;base64,' . $signatureData;
        }

        $savedFiles = [];
        $isIndonesian = false;
        $locale = 'en';

        // Set locale for translations
        app()->setLocale($locale);

        // Create a temporary data structure with signature
        $data = [
            'full_name' => $validated['full_name'],
            'citizen_id' => $validated['citizen_id'],
            'batch' => $validated['batch'],
            'signature_base64' => $signatureBase64,
        ];

        // Generate PDF with signature
        $pdfData = $this->generateAgreementPDF($data, $savedFiles, $isIndonesian, $locale, $signatureBase64);

        // Return PDF for preview (stream to browser)
        return response()->make($pdfData, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="preview-english.pdf"',
        ]);
    }
}
