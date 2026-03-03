<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReimbursementController extends Controller
{
    private function resolveRange(Request $request): array
    {
        $validRanges = ['week1', 'week2', 'week3', 'week4', 'custom', 'all'];
        $range = (string) $request->query('range', 'week3');
        if (!in_array($range, $validRanges, true)) {
            $range = 'week3';
        }

        $now = Carbon::now();
        $mondayThisWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weeks = [
            'week1' => [
                'start' => $mondayThisWeek->copy()->subWeeks(3)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(3)->addDays(6)->endOfDay(),
                'label' => __('messages.range_3_weeks_ago'),
            ],
            'week2' => [
                'start' => $mondayThisWeek->copy()->subWeeks(2)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(2)->addDays(6)->endOfDay(),
                'label' => __('messages.range_2_weeks_ago'),
            ],
            'week3' => [
                'start' => $mondayThisWeek->copy()->subWeeks(1)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(1)->addDays(6)->endOfDay(),
                'label' => __('messages.range_last_week'),
            ],
            'week4' => [
                'start' => $mondayThisWeek->copy()->startOfDay(),
                'end' => $mondayThisWeek->copy()->addDays(6)->endOfDay(),
                'label' => __('messages.range_this_week'),
            ],
        ];

        $fromInput = (string) $request->query('from', '');
        $toInput = (string) $request->query('to', '');
        $start = null;
        $end = null;

        switch ($range) {
            case 'all':
                break;
            case 'custom':
                if ($fromInput !== '' && $toInput !== '') {
                    $start = Carbon::parse($fromInput)->startOfDay();
                    $end = Carbon::parse($toInput)->endOfDay();
                }
                break;
            case 'week1':
            case 'week2':
            case 'week3':
            case 'week4':
            default:
                $start = $weeks[$range]['start'];
                $end = $weeks[$range]['end'];
                break;
        }

        $rangeLabel = $range === 'all'
            ? __('messages.range_all')
            : ($start && $end
                ? ($start->copy()->locale(app()->getLocale())->translatedFormat('d M Y') . ' – ' . $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y'))
                : '-');

        return [$range, $start, $end, (string) $rangeLabel, $weeks, $fromInput, $toInput];
    }

    private function assertNotTrainee(): void
    {
        $position = (string) session('user.position', '');
        if (Str::of($position)->lower()->contains('trainee')) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $this->assertNotTrainee();

        $userId = (int) ($user['id'] ?? 0);
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isStaff = $userRole === 'staff';
        $isDirector = in_array($userRole, ['vice director', 'director'], true);
        $canPay = !$isStaff;

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        $query = DB::table('reimbursements as r')
            ->leftJoin('user_rh as paid', 'paid.id', '=', 'r.paid_by')
            ->leftJoin('user_rh as cby', 'cby.id', '=', 'r.created_by')
            ->selectRaw('r.reimbursement_code AS reimbursement_code')
            ->selectRaw('MAX(r.billing_source_type) AS billing_source_type')
            ->selectRaw('MAX(r.billing_source_name) AS billing_source_name')
            ->selectRaw('MAX(r.item_name) AS item_name')
            ->selectRaw('MAX(r.status) AS status')
            ->selectRaw('MIN(r.created_at) AS created_at')
            ->selectRaw('COALESCE(SUM(r.amount), 0) AS total_amount')
            ->selectRaw('MAX(r.receipt_file) AS receipt_file')
            ->selectRaw('MAX(r.paid_at) AS paid_at')
            ->selectRaw('MAX(paid.full_name) AS paid_by_name')
            ->selectRaw('MAX(r.created_by) AS created_by_id')
            ->selectRaw('MAX(cby.full_name) AS created_by_name')
            ->groupBy('r.reimbursement_code')
            ->orderByRaw('MIN(r.created_at) DESC');

        if ($isStaff) {
            $query->where('r.created_by', $userId);
        }

        if ($range !== 'all' && $start && $end) {
            $query->whereBetween('r.created_at', [$start, $end]);
        }

        $rows = $query->limit(1000)->get();

        return view('pages.reimbursement.index', [
            'rows' => $rows,
            'range' => $range,
            'weeks' => $weeks,
            'rangeLabel' => $rangeLabel,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'userRole' => $userRole,
            'isStaff' => $isStaff,
            'isDirector' => $isDirector,
            'canPay' => $canPay,
            'defaultCode' => 'REIMB-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(3)),
        ]);
    }

    public function store(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $this->assertNotTrainee();

        $validated = $request->validate([
            'reimbursement_code' => ['required', 'string', 'max:50'],
            'billing_source_type' => ['required', 'in:instansi,restoran,toko,vendor,lainnya'],
            'billing_source_name' => ['required', 'string', 'max:255'],
            'item_name' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'integer', 'min:1', 'max:9999'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'receipt_file' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        $qty = (int) $validated['qty'];
        $price = (float) $validated['price'];
        $amount = round($qty * $price, 2);

        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $file = $request->file('receipt_file');
            if ($file && $file->isValid()) {
                $stored = $file->store('reimbursements', 'public');
                $receiptPath = 'storage/' . $stored;
            }
        }

        DB::table('reimbursements')->insert([
            'reimbursement_code' => (string) $validated['reimbursement_code'],
            'billing_source_type' => (string) $validated['billing_source_type'],
            'billing_source_name' => (string) $validated['billing_source_name'],
            'item_name' => (string) $validated['item_name'],
            'qty' => $qty,
            'price' => $price,
            'amount' => $amount,
            'receipt_file' => $receiptPath,
            'status' => 'submitted',
            'created_by' => (int) $user['id'],
            'submitted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', __('messages.reimbursement_saved'));
    }

    public function pay(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $this->assertNotTrainee();

        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if ($role === 'staff') {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $updated = DB::table('reimbursements')
            ->where('reimbursement_code', (string) $validated['code'])
            ->where('status', 'submitted')
            ->update([
                'status' => 'paid',
                'paid_by' => (int) $user['id'],
                'paid_at' => now(),
                'updated_at' => now(),
            ]);

        if ($updated <= 0) {
            return back()->with('info', __('messages.reimbursement_nothing_to_pay'));
        }

        return back()->with('success', __('messages.reimbursement_paid'));
    }

    public function destroy(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $this->assertNotTrainee();

        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if (!in_array($role, ['vice director', 'director'], true)) {
            abort(403);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = (string) $validated['code'];

        $paths = DB::table('reimbursements')
            ->where('reimbursement_code', $code)
            ->pluck('receipt_file')
            ->filter(fn ($p) => is_string($p) && $p !== '')
            ->values()
            ->all();

        $deleted = DB::table('reimbursements')->where('reimbursement_code', $code)->delete();

        foreach ($paths as $p) {
            $p = (string) $p;
            if (!Str::startsWith($p, 'storage/reimbursements/')) {
                continue;
            }
            $diskPath = Str::after($p, 'storage/');
            try {
                Storage::disk('public')->delete($diskPath);
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return back()->with('success', __('messages.reimbursement_deleted', ['count' => (int) $deleted]));
    }

    public function receipt(string $code)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user', []);
        if (empty($user['id'])) {
            abort(401);
        }

        $this->assertNotTrainee();

        $userId = (int) ($user['id'] ?? 0);
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isStaff = $role === 'staff';

        $q = DB::table('reimbursements')
            ->where('reimbursement_code', $code)
            ->whereNotNull('receipt_file');
        if ($isStaff) {
            $q->where('created_by', $userId);
        }

        $receipt = (string) ($q->value('receipt_file') ?? '');
        if ($receipt === '') {
            abort(404);
        }

        $rel = $receipt;
        if (Str::startsWith($rel, '/')) {
            $rel = ltrim($rel, '/');
        }
        if (Str::startsWith($rel, 'storage/')) {
            $rel = Str::after($rel, 'storage/');
        }

        if (!Str::startsWith($rel, 'reimbursements/')) {
            abort(404);
        }

        if (!Storage::disk('public')->exists($rel)) {
            abort(404);
        }

        $path = Storage::disk('public')->path($rel);
        return response()->file($path, [
            'Cache-Control' => 'private, max-age=86400',
        ]);
    }
}
