<?php

namespace App\Http\Controllers;

use App\Models\MedicOperasiPlastik;
use App\Models\UserRh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OperasiPlastikController extends Controller
{
    private const JENIS_OPERASI = [
        'Rekonstruksi Wajah',
        'Suntik Putih',
    ];

    private function decodeAcademyDocsRaw(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
            return [];
        }

        if (isset($decoded['academy']) && is_array($decoded['academy'])) {
            return array_values(array_filter($decoded['academy'], fn ($d) => is_array($d)));
        }

        if (array_is_list($decoded)) {
            return array_values(array_filter($decoded, fn ($d) => is_array($d)));
        }

        return [];
    }

    private function hasPlasticSurgeryCertificate(?string $dokumenLainnya): bool
    {
        // Only accept Academy certificates. Ignore legacy `dokumen_lainnya` string paths.
        foreach ($this->decodeAcademyDocsRaw($dokumenLainnya) as $doc) {
            $name = is_string($doc['name'] ?? null) ? trim((string) $doc['name']) : '';
            if ($name === '') {
                continue;
            }

            $n = Str::of($name)->lower()->toString();
            $id = str_contains($n, 'operasi') && str_contains($n, 'plastik');
            $en1 = str_contains($n, 'plastic') && str_contains($n, 'surgery');
            $en2 = str_contains($n, 'cosmetic') && str_contains($n, 'surgery');
            if ($id || $en1 || $en2) {
                return true;
            }
        }

        return false;
    }

    private function isCertificateExempt(UserRh $u): bool
    {
        $role = Str::of((string) ($u->role ?? ''))->lower()->trim()->toString();
        $position = Str::of((string) ($u->position ?? ''))->lower()->trim()->toString();

        if ($role !== '' && (str_contains($role, 'manager') || str_contains($role, 'director'))) {
            return true;
        }

        if (
            str_contains($position, 'general doctor') ||
            (str_contains($position, 'dokter') && str_contains($position, 'umum'))
        ) {
            return true;
        }

        return false;
    }

    private function canBeHandler(UserRh $u): bool
    {
        $position = Str::of((string) ($u->position ?? ''))->lower()->trim()->toString();
        if ($position !== '' && (str_contains($position, 'trainee') || str_contains($position, 'paramedic'))) {
            return false;
        }

        $role = Str::of((string) ($u->role ?? ''))->lower()->trim()->toString();
        if ($role !== '' && (str_contains($role, 'manager') || str_contains($role, 'director'))) {
            return true;
        }

        return $this->isOplasHandlerPosition($u->position);
    }

    private function isOplasHandlerPosition(?string $position): bool
    {
        $p = Str::of((string) $position)->lower()->trim()->toString();

        if ($p === '') {
            return false;
        }

        if (str_contains($p, 'trainee') || str_contains($p, 'paramedic')) {
            return false;
        }

        if (str_contains($p, 'manager')) {
            return true;
        }

        if (str_contains($p, 'co.ast') || str_contains($p, 'co-ast') || str_contains($p, 'coast')) {
            return true;
        }

        if (str_contains($p, 'general doctor') || (str_contains($p, 'dokter') && str_contains($p, 'umum'))) {
            return true;
        }

        if (
            str_contains($p, 'specialist doctor') ||
            (str_contains($p, 'dokter') && str_contains($p, 'spesialis')) ||
            (str_contains($p, 'doctor') && str_contains($p, 'specialist'))
        ) {
            return true;
        }

        return false;
    }

    private function userUi(): array
    {
        $user = session('user', []);
        return is_array($user) ? $user : [];
    }

    private function requireLogin()
    {
        $user = $this->userUi();
        if (empty($user['id'])) {
            return redirect()->route('login');
        }
        return null;
    }

    private function canApproveActions(): bool
    {
        $role = Str::of((string) session('user.role', ''))->lower()->trim()->toString();
        $position = Str::of((string) session('user.position', ''))->lower()->trim()->toString();

        $isManager = str_contains($role, 'manager') || str_contains($role, 'director');
        $isBasic = str_contains($position, 'trainee') || str_contains($position, 'paramedic');
        $notBasic = !$isBasic;

        return $isManager || $notBasic;
    }

    private function eligibleForDate(int $userId, Carbon $tanggal): array
    {
        $start = $tanggal->copy()->startOfMonth()->startOfDay();
        $end = $tanggal->copy()->endOfMonth()->endOfDay();

        $exists = MedicOperasiPlastik::query()
            ->where('id_user', $userId)
            ->whereBetween('tanggal', [$start->toDateString(), $end->toDateString()])
            ->exists();

        if (!$exists) {
            return [true, 0, null];
        }

        $nextEligible = $start->copy()->addMonth()->startOfMonth();
        $remainingDays = Carbon::today()->diffInDays($nextEligible, false);
        if ($remainingDays < 0) {
            $remainingDays = 0;
        }

        return [false, $remainingDays, $nextEligible];
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $user = $this->userUi();
        $userId = (int) ($user['id'] ?? 0);
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isStaff = $userRole === 'staff';

        $penanggungJawabRaw = UserRh::query()
            ->select(['id', 'full_name', 'position', 'role', 'dokumen_lainnya'])
            ->orderBy('full_name', 'asc')
            ->get();

        $penanggungJawab = $penanggungJawabRaw
            ->filter(function (UserRh $u) {
                if (!$this->canBeHandler($u)) {
                    return false;
                }
                if ($this->isCertificateExempt($u)) {
                    return true;
                }
                return $this->hasPlasticSurgeryCertificate($u->dokumen_lainnya);
            })
            ->values();

        $penanggungJawabOptions = $penanggungJawab
            ->map(fn (UserRh $pj) => [
                'value' => (string) $pj->id,
                'label' => (string) ($pj->full_name . ' (' . ($pj->position ?? '-') . ')'),
            ])
            ->all();

        $q = MedicOperasiPlastik::query()
            ->with([
                'user:id,full_name,position,role',
                'penanggungJawab:id,full_name,position,role',
                'approvedBy:id,full_name,position,role',
            ])
            ->orderByDesc('created_at');

        if ($isStaff) {
            $q->where('id_user', $userId);
        }

        $rows = $q->get();

        [$canInput, $remainingDays, $nextEligible] = $this->eligibleForDate($userId, Carbon::today());

        return view('pages.medis.operasi-plastik', [
            'user' => $user,
            'rows' => $rows,
            'penanggungJawabOptions' => $penanggungJawabOptions,
            'jenisOperasi' => self::JENIS_OPERASI,
            'canInput' => $canInput,
            'remainingDays' => $remainingDays,
            'nextEligible' => $nextEligible,
            'canApprove' => $this->canApproveActions(),
            'isStaff' => $isStaff,
        ]);
    }

    public function store(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $user = $this->userUi();
        $userId = (int) ($user['id'] ?? 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis_operasi' => ['required', 'in:' . implode(',', self::JENIS_OPERASI)],
            'id_penanggung_jawab' => ['nullable', 'integer', 'exists:user_rh,id'],
            'penanggung_jawab_name' => ['required', 'string', 'max:200'],
            'alasan' => ['required', 'string', 'max:2000'],
        ]);

        $tanggal = Carbon::parse((string) $validated['tanggal'])->startOfDay();
        [$ok] = $this->eligibleForDate($userId, $tanggal);
        if (!$ok) {
            throw ValidationException::withMessages([
                'tanggal' => __('messages.operasi_plastik_limit_monthly'),
            ]);
        }

        $handlerId = (int) ($validated['id_penanggung_jawab'] ?? 0);
        $pj = null;

        if ($handlerId > 0) {
            $pj = UserRh::query()
                ->select(['id', 'full_name', 'position', 'role', 'dokumen_lainnya'])
                ->where('id', $handlerId)
                ->first();
        } else {
            $rawName = trim((string) ($validated['penanggung_jawab_name'] ?? ''));
            $nameOnly = preg_replace('/\\s*\\([^)]*\\)\\s*$/', '', $rawName);
            $nameOnly = trim((string) $nameOnly);

            if ($nameOnly !== '') {
                $pj = UserRh::query()
                    ->select(['id', 'full_name', 'position', 'role', 'dokumen_lainnya'])
                    ->where('full_name', $nameOnly)
                    ->first();
                if ($pj) {
                    $handlerId = (int) $pj->id;
                }
            }
        }

        if (
            !$pj ||
            !$this->canBeHandler($pj) ||
            (!$this->isCertificateExempt($pj) && !$this->hasPlasticSurgeryCertificate($pj->dokumen_lainnya))
        ) {
            throw ValidationException::withMessages([
                'id_penanggung_jawab' => __('messages.operasi_plastik_handler_invalid'),
            ]);
        }

        MedicOperasiPlastik::query()->create([
            'id_user' => $userId,
            'tanggal' => $tanggal->toDateString(),
            'jenis_operasi' => (string) $validated['jenis_operasi'],
            'alasan' => (string) $validated['alasan'],
            'status' => 'pending',
            'id_penanggung_jawab' => $handlerId,
            'created_at' => now(),
        ]);

        return back()->with('success', __('messages.operasi_plastik_saved'));
    }

    public function approve(Request $request, MedicOperasiPlastik $operasi)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $userId = (int) session('user.id', 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        // Only the assigned handler (penanggung jawab) can approve.
        if ((int) $operasi->id_penanggung_jawab !== $userId) {
            abort(403);
        }

        if ($operasi->status !== 'pending') {
            return back()->with('error', __('messages.operasi_plastik_not_pending'));
        }

        $operasi->approve($userId);

        return back()->with('success', __('messages.operasi_plastik_approved'));
    }

    public function reject(Request $request, MedicOperasiPlastik $operasi)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $userId = (int) session('user.id', 0);
        if ($userId <= 0) {
            return redirect()->route('login');
        }

        // Only the assigned handler (penanggung jawab) can reject.
        if ((int) $operasi->id_penanggung_jawab !== $userId) {
            abort(403);
        }

        if ($operasi->status !== 'pending') {
            return back()->with('error', __('messages.operasi_plastik_not_pending'));
        }

        $operasi->reject($userId);

        return back()->with('success', __('messages.operasi_plastik_rejected'));
    }
}
