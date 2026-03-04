<?php

namespace App\Http\Controllers;

use App\Models\UserRh;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $locale = Session::get('locale', 'id');
        app()->setLocale($locale);

        $userId = (int) session('user.id', 0);
        $userRh = $userId > 0 ? UserRh::query()->find($userId) : null;

        return view('pages.settings.index', [
            'userRh' => $userRh,
            'canManageWebSettings' => $this->canManageWebSettings(),
        ]);
    }

    public function updateAccount(Request $request)
    {
        $locale = Session::get('locale', 'id');
        app()->setLocale($locale);

        // Normalize early so validation (including unique checks) uses consistent casing.
        $request->merge([
            'full_name' => ucwords(strtolower((string) $request->input('full_name', ''))),
            'citizen_id' => strtoupper((string) $request->input('citizen_id', '')),
            'position' => $this->normalizePosition((string) $request->input('position', '')),
        ]);

        $userId = (int) session('user.id', 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        $user = UserRh::query()->find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', __('messages.session_expired', ['Your session has expired. Please login again.']));
        }

        $anyPinFilled = trim((string) $request->input('old_pin', '')) !== ''
            || trim((string) $request->input('new_pin', '')) !== ''
            || trim((string) $request->input('confirm_pin', '')) !== '';

        $rules = [
            'full_name' => 'required|string|max:100',
            'position' => ['required', 'string', Rule::in($this->allowedPositions())],
            'tanggal_masuk' => 'required|date',
            'citizen_id' => 'required|string|max:30|regex:/^[A-Z0-9]+$/|unique:user_rh,citizen_id,' . $user->id,
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'no_hp_ic' => 'required|string|max:20',

            'batch' => 'required|integer|min:1|max:26',

            // Supporting documents (images)
            'sertifikat_heli' => 'nullable|file|mimes:png,jpeg,jpg|max:5120',
            'sertifikat_operasi' => 'nullable|file|mimes:png,jpeg,jpg|max:5120',

            'academy_doc_id' => 'nullable|array',
            'academy_doc_id.*' => 'nullable|string|max:80',
            'academy_doc_name' => 'nullable|array',
            'academy_doc_name.*' => 'nullable|string|max:150',
            'academy_doc_file' => 'nullable|array',
            'academy_doc_file.*' => 'nullable|file|mimes:png,jpeg,jpg|max:5120',
            'academy_doc_delete' => 'nullable|array',
            'academy_doc_delete.*' => 'nullable|string|max:80',
        ];

        if ($anyPinFilled) {
            $rules = array_merge($rules, [
                'old_pin' => 'required|string|size:4|regex:/^[0-9]+$/',
                'new_pin' => 'required|string|size:4|regex:/^[0-9]+$/|different:old_pin',
                'confirm_pin' => 'required|string|same:new_pin',
            ]);
        } else {
            $rules = array_merge($rules, [
                'old_pin' => 'nullable|string|size:4|regex:/^[0-9]+$/',
                'new_pin' => 'nullable|string|size:4|regex:/^[0-9]+$/',
                'confirm_pin' => 'nullable|string|size:4|regex:/^[0-9]+$/',
            ]);
        }

        $validated = $request->validate($rules);

        // Normalized via request->merge() before validation

        // Determine upload folder (prefer existing folder to avoid breaking old file paths)
        $existingPath = $user->photo_profile
            ?? $user->sertifikat_heli
            ?? $user->sertifikat_operasi
            ?? $this->firstAcademyDocPath((string) ($user->dokumen_lainnya ?? ''))
            ?? $user->signature
            ?? $user->ttd;

        $folderName = null;
        if (is_string($existingPath) && str_contains($existingPath, 'storage/user_docs/')) {
            $after = explode('storage/user_docs/', $existingPath, 2)[1] ?? '';
            $folderName = explode('/', $after, 2)[0] ?? null;
            $folderName = $folderName !== '' ? $folderName : null;
        }

        if (!$folderName) {
            $sanitizedName = strtolower(str_replace(' ', '_', $validated['full_name']));
            $folderName = 'user_' . $user->id . '-' . $sanitizedName . '-' . $validated['citizen_id'];
        }

        $baseDir = public_path('storage/user_docs/');
        $uploadDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $folderName;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            return back()->with('error', __('messages.failed_create_docs_folder'))->withInput();
        }

        // Update main profile fields
        $user->full_name = $validated['full_name'];
        $user->position = $validated['position'];
        $user->tanggal_masuk = $validated['tanggal_masuk'];
        $user->citizen_id = $validated['citizen_id'];
        $user->jenis_kelamin = $validated['jenis_kelamin'];
        $user->no_hp_ic = $validated['no_hp_ic'];
        $user->batch = (int) $validated['batch'];

        // Update PIN (optional)
        if ($anyPinFilled) {
            $oldPin = (string) $validated['old_pin'];
            $newPin = (string) $validated['new_pin'];

            $pinValid = false;
            $storedPin = (string) $user->getRawOriginal('pin');

            try {
                if ($storedPin !== '' && Hash::check($oldPin, $storedPin)) {
                    $pinValid = true;
                }
            } catch (\RuntimeException $e) {
                // Stored PIN may be plaintext or use a different hashing algorithm.
                // Fall back to the plaintext compatibility check below.
            }

            if (!$pinValid && hash_equals($storedPin, $oldPin)) {
                $pinValid = true;
            }

            if (!$pinValid) {
                return back()->withErrors(['old_pin' => __('messages.pin_incorrect')])->withInput();
            }

            $user->pin = Hash::make($newPin);
            $user->pin_changed = true;
        }

        // Save uploaded documents (replace existing)
        $docFields = [
            'sertifikat_heli' => 'sertifikat_heli.jpg',
            'sertifikat_operasi' => 'sertifikat_operasi.jpg',
        ];

        foreach ($docFields as $field => $fileName) {
            if (!$request->hasFile($field)) {
                continue;
            }

            $file = $request->file($field);
            if (!$file || !$file->isValid()) {
                continue;
            }

            $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
            if (!$this->compressImageSmart($file->getPathname(), $targetPath)) {
                return back()->with('error', __('messages.failed_compress_document', ['doc' => $field]))->withInput();
            }

            $user->{$field} = 'storage/user_docs/' . $folderName . '/' . $fileName;
        }

        $academyResult = $this->applyAcademyDocsUpdate(
            raw: (string) ($user->dokumen_lainnya ?? ''),
            request: $request,
            folderName: $folderName,
            uploadDir: $uploadDir
        );

        if (!$academyResult['ok']) {
            return back()->with('error', (string) $academyResult['message'])->withInput();
        }

        $user->dokumen_lainnya = (string) $academyResult['raw'];

        $user->save();

        // Keep UI session in sync
        Session::put('user.name', $user->full_name);
        Session::put('user.citizen_id', $user->citizen_id);
        Session::put('user.batch', $user->batch);
        Session::put('user.position', $user->position ?? 'Trainee');

        return back()->with('success', __('messages.account_settings_saved'));
    }

    public function updateWeb(Request $request)
    {
        $locale = Session::get('locale', 'id');
        app()->setLocale($locale);

        if (! $this->canManageWebSettings()) {
            return back()->with('error', __('messages.web_settings_access_denied'));
        }

        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:150', 'not_regex:/[|]/'],
            'app_tagline' => ['nullable', 'string', 'max:150', 'not_regex:/[|]/'],
            'timezone' => ['nullable', 'timezone'],
            'app_logo' => ['nullable', 'file', 'mimes:png,jpeg,jpg,svg', 'max:5120'],
        ]);

        $setting = AppSetting::query()->first() ?? new AppSetting();
        $main = trim((string) $validated['app_name']);
        $tagline = trim((string) ($validated['app_tagline'] ?? ''));
        $setting->app_name = $tagline !== '' ? ($main . '|' . $tagline) : $main;
        $setting->timezone = $validated['timezone'] ?? null;

        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            if ($file && $file->isValid()) {
                $ext = strtolower((string) $file->getClientOriginalExtension());
                if ($ext === '') {
                    $ext = 'png';
                }
                $stored = $file->storeAs('app_settings', 'logo.' . $ext, 'public');
                $setting->app_logo_path = 'storage/' . ltrim($stored, '/');
            }
        }

        $setting->save();

        try {
            Cache::forget('app_settings:current');
        } catch (\Throwable $e) {
            // Ignore cache backend issues (e.g., missing database cache table).
        }

        return back()->with('success', __('messages.web_settings_saved'));
    }

    private function canManageWebSettings(): bool
    {
        $roleRaw = (string) session('user.role', '');
        $roleNorm = Str::of($roleRaw)->lower()->replace([' ', '_', '-'], '')->toString();

        return in_array($roleNorm, ['director', 'vicedirector', 'vicedirecture'], true);
    }

    private function allowedPositions(): array
    {
        return [
            'Trainee',
            'Paramedic',
            '(Co.Ast)',
            'General Doctor',
            'Specialist Doctor',
        ];
    }

    private function normalizePosition(string $value): string
    {
        $raw = Str::of($value)->lower()->trim()->toString();
        $raw = str_replace(['_', '-'], ' ', $raw);

        if ($raw === '') {
            return '';
        }

        if (str_contains($raw, 'trainee')) {
            return 'Trainee';
        }

        if (str_contains($raw, 'paramedic')) {
            return 'Paramedic';
        }

        if (str_contains($raw, 'co') && str_contains($raw, 'ast')) {
            return '(Co.Ast)';
        }

        if (
            str_contains($raw, 'general doctor') ||
            str_contains($raw, 'dokter umum') ||
            str_contains($raw, 'doctor umum')
        ) {
            return 'General Doctor';
        }

        if (
            str_contains($raw, 'specialist doctor') ||
            str_contains($raw, 'dokter spesialis') ||
            str_contains($raw, 'doctor specialist') ||
            str_contains($raw, 'dokter specialist')
        ) {
            return 'Specialist Doctor';
        }

        // Fallback: keep original (will be validated)
        return trim($value);
    }

    private function decodeAcademyDocs(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return ['academy' => [], 'legacy' => ''];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            if (is_array($decoded) && isset($decoded['academy']) && is_array($decoded['academy'])) {
                return [
                    'academy' => array_values(array_filter($decoded['academy'], fn ($d) => is_array($d))),
                    'legacy' => is_string($decoded['legacy'] ?? null) ? (string) $decoded['legacy'] : '',
                ];
            }

            // If it's a list array, treat it as academy docs (legacy JSON format).
            if (is_array($decoded) && array_is_list($decoded)) {
                return ['academy' => array_values(array_filter($decoded, fn ($d) => is_array($d))), 'legacy' => ''];
            }
        }

        // Unknown legacy string: preserve, but do not treat as academy list.
        return ['academy' => [], 'legacy' => $raw];
    }

    private function encodeAcademyDocs(array $academy, string $legacy = ''): string
    {
        $payload = [
            'academy' => array_values($academy),
        ];
        if (trim($legacy) !== '') {
            $payload['legacy'] = $legacy;
        }

        return (string) json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    private function firstAcademyDocPath(string $raw): ?string
    {
        $decoded = $this->decodeAcademyDocs($raw);
        foreach (($decoded['academy'] ?? []) as $d) {
            $path = is_string($d['path'] ?? null) ? trim((string) $d['path']) : '';
            if ($path !== '') {
                return $path;
            }
        }

        return null;
    }

    private function applyAcademyDocsUpdate(string $raw, Request $request, string $folderName, string $uploadDir): array
    {
        $decoded = $this->decodeAcademyDocs($raw);
        $academy = is_array($decoded['academy'] ?? null) ? $decoded['academy'] : [];
        $legacy = is_string($decoded['legacy'] ?? null) ? (string) $decoded['legacy'] : '';

        // Build map by id
        $byId = [];
        foreach ($academy as $doc) {
            $id = is_string($doc['id'] ?? null) ? trim((string) $doc['id']) : '';
            if ($id === '') {
                continue;
            }
            $byId[$id] = [
                'id' => $id,
                'name' => is_string($doc['name'] ?? null) ? (string) $doc['name'] : '',
                'path' => is_string($doc['path'] ?? null) ? (string) $doc['path'] : '',
            ];
        }

        $ids = (array) $request->input('academy_doc_id', []);
        $names = (array) $request->input('academy_doc_name', []);
        $files = (array) ($request->file('academy_doc_file', []) ?? []);
        $delete = array_map('strval', (array) $request->input('academy_doc_delete', []));
        $delete = array_values(array_filter(array_map('trim', $delete), fn ($v) => $v !== ''));
        $deleteSet = array_flip($delete);

        $next = [];

        $rows = max(count($ids), count($names), count($files));
        for ($i = 0; $i < $rows; $i++) {
            $id = isset($ids[$i]) ? trim((string) $ids[$i]) : '';
            $name = isset($names[$i]) ? trim((string) $names[$i]) : '';
            $file = $files[$i] ?? null;

            if ($id !== '' && isset($deleteSet[$id])) {
                // Delete existing
                $existing = $byId[$id] ?? null;
                if ($existing && is_string($existing['path'] ?? null)) {
                    $path = trim((string) $existing['path']);
                    if ($path !== '' && str_starts_with($path, 'storage/user_docs/')) {
                        @unlink(public_path($path));
                    }
                }
                continue;
            }

            if ($id === '') {
                // New row: allow empty row
                $hasFile = $file && method_exists($file, 'isValid') && $file->isValid();
                if ($name === '' && !$hasFile) {
                    continue;
                }
                if ($name === '' || !$hasFile) {
                    return ['ok' => false, 'message' => __('messages.academy_certificate_incomplete')];
                }

                $id = (string) Str::uuid();
                $fileName = 'academy_' . $id . '.jpg';
                $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                if (!$this->compressImageSmart($file->getPathname(), $targetPath)) {
                    return ['ok' => false, 'message' => __('messages.failed_compress_document', ['doc' => 'academy_doc_file'])];
                }

                $next[] = [
                    'id' => $id,
                    'name' => $name,
                    'path' => 'storage/user_docs/' . $folderName . '/' . $fileName,
                ];
                continue;
            }

            // Existing row
            $existing = $byId[$id] ?? ['id' => $id, 'name' => '', 'path' => ''];
            if ($name !== '') {
                $existing['name'] = $name;
            }

            if ($file && method_exists($file, 'isValid') && $file->isValid()) {
                $fileName = 'academy_' . $id . '.jpg';
                $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;
                if (!$this->compressImageSmart($file->getPathname(), $targetPath)) {
                    return ['ok' => false, 'message' => __('messages.failed_compress_document', ['doc' => 'academy_doc_file'])];
                }
                $existing['path'] = 'storage/user_docs/' . $folderName . '/' . $fileName;
            }

            $next[] = $existing;
        }

        // Keep any existing docs not referenced by the form (avoid accidental loss)
        $seen = array_flip(array_map(fn ($d) => (string) ($d['id'] ?? ''), $next));
        foreach ($byId as $id => $doc) {
            if ($id === '' || isset($seen[$id])) {
                continue;
            }
            if (isset($deleteSet[$id])) {
                continue;
            }
            $next[] = $doc;
        }

        return [
            'ok' => true,
            'raw' => $this->encodeAcademyDocs($next, $legacy),
        ];
    }

    /**
     * Compress image smart - compress under 300KB while maintaining quality.
     */
    private function compressImageSmart(
        string $sourcePath,
        string $targetPath,
        int $maxWidth = 1200,
        int $targetSize = 300000,
        int $minQuality = 65
    ): bool {
        try {
            $info = getimagesize($sourcePath);
            if (!$info) {
                return false;
            }

            $mime = $info['mime'] ?? null;

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

            if ($width > $maxWidth) {
                $ratio = $maxWidth / $width;
                $newWidth = $maxWidth;
                $newHeight = (int) round($height * $ratio);

                $newImage = imagecreatetruecolor($newWidth, $newHeight);

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

            $quality = 85;
            $maxIterations = 10;

            while ($quality >= $minQuality && $maxIterations > 0) {
                $tempPath = $targetPath . '.tmp';
                imagejpeg($image, $tempPath, $quality);

                $fileSize = @filesize($tempPath);
                if ($fileSize !== false && $fileSize <= $targetSize) {
                    rename($tempPath, $targetPath);
                    imagedestroy($image);
                    return true;
                }

                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }

                $quality -= 5;
                $maxIterations--;
            }

            imagejpeg($image, $targetPath, $minQuality);
            imagedestroy($image);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
