<?php

namespace App\Http\Controllers;

use App\Models\EmsSale;
use App\Models\MedicalRegulation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EmsServicesController extends Controller
{
    private const RS_DEFAULT_LOCATION = '4017';

    private const SERVICE_TYPES = [
        'Pingsan',
        'Treatment',
        'Surat',
        'Operasi',
        'Rawat Inap',
        'Kematian',
        'Plastik',
    ];

    private const MED_AREAS = [
        'Head',
        'Body',
        'Left Arm',
        'Right Arm',
        'Left Leg',
        'Right Leg',
        'Left Foot',
        'Right Foot',
    ];

    private const MED_AREA_TO_ITEM = [
        'Head' => 'GAUZE',
        'Body' => 'GAUZE',
        'Left Arm' => 'IODINE',
        'Right Arm' => 'IODINE',
        'Left Leg' => 'SYRINGE',
        'Right Leg' => 'SYRINGE',
        'Left Foot' => 'SYRINGE',
        'Right Foot' => 'SYRINGE',
    ];

    private function resolveRange(Request $request): array
    {
        $validRanges = ['today', 'yesterday', 'last7', 'week1', 'week2', 'week3', 'week4', 'custom'];
        $range = (string) $request->query('range', 'today');
        if (!in_array($range, $validRanges, true)) {
            $range = 'today';
        }

        $now = Carbon::now();
        $mondayThisWeek = $now->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
        $weeks = [
            'week1' => [
                'start' => $mondayThisWeek->copy()->subWeeks(3)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(3)->addDays(6)->endOfDay(),
            ],
            'week2' => [
                'start' => $mondayThisWeek->copy()->subWeeks(2)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(2)->addDays(6)->endOfDay(),
            ],
            'week3' => [
                'start' => $mondayThisWeek->copy()->subWeeks(1)->startOfDay(),
                'end' => $mondayThisWeek->copy()->subWeeks(1)->addDays(6)->endOfDay(),
            ],
            'week4' => [
                'start' => $mondayThisWeek->copy()->startOfDay(),
                'end' => $mondayThisWeek->copy()->addDays(6)->endOfDay(),
            ],
        ];

        $fromInput = '';
        $toInput = '';

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                break;
            case 'last7':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'custom':
                $from = (string) $request->query('from', '');
                $to = (string) $request->query('to', '');
                $fromInput = $from;
                $toInput = $to;
                if ($from !== '' && $to !== '') {
                    $start = Carbon::parse($from)->startOfDay();
                    $end = Carbon::parse($to)->endOfDay();
                } else {
                    $start = $weeks['week4']['start'];
                    $end = $weeks['week4']['end'];
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

        $rangeLabel = $start->copy()->locale(app()->getLocale())->translatedFormat('d M Y')
            . ' – ' .
            $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y');

        return [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput];
    }

    private function activeRegulation(string $code): ?MedicalRegulation
    {
        return MedicalRegulation::query()
            ->where('code', $code)
            ->where('is_active', 1)
            ->first();
    }

    private function regulationFixedPrice(string $code): int
    {
        $reg = $this->activeRegulation($code);
        if (!$reg) {
            throw ValidationException::withMessages([
                'service_type' => __('messages.medis_regulation_missing') . ': ' . $code,
            ]);
        }
        return (int) ($reg->price_min ?? 0);
    }

    private function regulationRange(string $code): array
    {
        $reg = $this->activeRegulation($code);
        if (!$reg) {
            throw ValidationException::withMessages([
                'service_type' => __('messages.medis_regulation_missing') . ': ' . $code,
            ]);
        }

        $min = (int) ($reg->price_min ?? 0);
        $max = (int) ($reg->price_max ?? 0);
        if ($max < $min) {
            $max = $min;
        }

        return [$min, $max];
    }

    private function pickOperasiPrice(int $min, int $max, string $tingkat): int
    {
        if ($max <= $min) {
            return $min;
        }

        $span = $max - $min;
        $step = (int) floor($span / 3);
        if ($step < 1) {
            return random_int($min, $max);
        }

        $t = Str::of($tingkat)->lower()->toString();
        return match ($t) {
            'ringan' => random_int($min, $min + $step),
            'sedang' => random_int($min + $step + 1, min($max, $min + ($step * 2))),
            'berat' => random_int(min($max, $min + ($step * 2) + 1), $max),
            default => random_int($min, $max),
        };
    }

    private function countOccurrences(string $haystack, string $needle): int
    {
        if ($needle === '') return 0;
        $pattern = '/' . preg_quote($needle, '/') . '/i';
        $m = [];
        return preg_match_all($pattern, $haystack, $m) ?: 0;
    }

    private function buildMedicineUsage(string $serviceType, array $areas, bool $isGunshot): ?string
    {
        $type = (string) $serviceType;
        $parts = [];

        if ($type === 'Treatment') {
            $parts[] = 'BANDAGE';
        }

        if ($type === 'Pingsan') {
            $parts[] = 'P3K';
        }

        $itemParts = [];
        foreach ($areas as $area) {
            if (!isset(self::MED_AREA_TO_ITEM[$area])) continue;
            $itemParts[] = $area . '(' . self::MED_AREA_TO_ITEM[$area] . ')';
        }

        if (!empty($itemParts)) {
            $parts[] = implode(', ', $itemParts);
        } elseif ($type === 'Pingsan') {
            // For pingsan without extra meds, still keep P3K only.
        }

        if ($isGunshot) {
            $parts[] = '[PELURU]';
        }

        $usage = trim(implode('; ', array_values(array_filter($parts, fn ($p) => trim((string) $p) !== ''))));
        return $usage !== '' ? $usage : null;
    }

    private function calculate(array $input): array
    {
        $serviceType = (string) ($input['service_type'] ?? '');
        $serviceDetail = (string) ($input['service_detail'] ?? '');
        $operasiTingkat = Str::of((string) ($input['operasi_tingkat'] ?? ''))->lower()->toString();
        $patientName = trim((string) ($input['patient_name'] ?? ''));
        $qty = (int) ($input['qty'] ?? 1);
        $paymentTypeIn = Str::of((string) ($input['payment_type'] ?? ''))->lower()->toString();
        $locationIn = trim((string) ($input['location'] ?? ''));
        $isGunshot = (bool) ($input['is_gunshot'] ?? false);
        $areas = array_values(array_filter((array) ($input['meds'] ?? []), fn ($v) => is_string($v) && in_array($v, self::MED_AREAS, true)));

        if (!in_array($serviceType, self::SERVICE_TYPES, true)) {
            throw ValidationException::withMessages(['service_type' => __('messages.medis_invalid_service_type')]);
        }

        if ($qty < 1) $qty = 1;

        $billingAmount = 0;
        $cashAmount = 0;
        $doctorShare = 0;
        $teamShare = 0;

        $basePrice = 0;
        $total = 0;
        $finalPaymentType = $paymentTypeIn;
        $finalLocation = $locationIn;
        $finalOperasiTingkat = null;
        $medicineUsage = null;

        $bleedingCode = $isGunshot ? 'BLEEDING_PELURU' : 'BLEEDING_OBAT';
        $bleedingPerItem = 0;
        if (in_array($serviceType, ['Pingsan', 'Treatment'], true)) {
            $bleedingPerItem = $this->regulationFixedPrice($bleedingCode);
        }

        switch ($serviceType) {
            case 'Pingsan': {
                $priceMap = [
                    'RS' => 'PP_RS',
                    'Paleto' => 'PP_PALETO',
                    'Gunung/Laut' => 'PP_GUNUNG',
                    'Zona Perang' => 'PP_PERANG',
                    'UFC' => 'PP_UFC',
                ];
                if (!isset($priceMap[$serviceDetail])) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }

                $basePrice = $this->regulationFixedPrice($priceMap[$serviceDetail]);
                $total = $basePrice + (count($areas) * $bleedingPerItem);

                $finalPaymentType = 'cash';
                if ($serviceDetail === 'RS') {
                    $finalLocation = self::RS_DEFAULT_LOCATION;
                }

                $medicineUsage = $this->buildMedicineUsage('Pingsan', $areas, $isGunshot);
                break;
            }

            case 'Treatment': {
                if (!in_array($serviceDetail, ['RS', 'Luar'], true)) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }

                $code = $serviceDetail === 'RS' ? 'TR_RS' : 'TR_LUAR';
                $basePrice = $this->regulationFixedPrice($code);
                $total = $basePrice + (count($areas) * $bleedingPerItem);

                $finalPaymentType = 'cash';
                if ($serviceDetail === 'RS') {
                    $finalLocation = self::RS_DEFAULT_LOCATION;
                }

                $medicineUsage = $this->buildMedicineUsage('Treatment', $areas, $isGunshot);
                break;
            }

            case 'Surat': {
                if (!in_array($serviceDetail, ['Kesehatan', 'Psikologi'], true)) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }
                $code = $serviceDetail === 'Kesehatan' ? 'SK_KES' : 'SK_PSI';
                $basePrice = $this->regulationFixedPrice($code);
                $total = $basePrice;

                $finalPaymentType = 'cash';
                $finalLocation = self::RS_DEFAULT_LOCATION;
                break;
            }

            case 'Operasi': {
                if (!in_array($serviceDetail, ['Besar', 'Kecil'], true)) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }
                if (!in_array($operasiTingkat, ['ringan', 'sedang', 'berat'], true)) {
                    throw ValidationException::withMessages(['operasi_tingkat' => __('messages.medis_operasi_tingkat_required')]);
                }

                $code = $serviceDetail === 'Besar' ? 'OP_BESAR' : 'OP_KECIL';
                [$min, $max] = $this->regulationRange($code);
                $basePrice = $this->pickOperasiPrice($min, $max, $operasiTingkat);
                $total = $basePrice;

                $finalPaymentType = 'billing';
                $finalLocation = self::RS_DEFAULT_LOCATION;
                $finalOperasiTingkat = $operasiTingkat;

                $billingAmount = (int) floor($total / 2);
                $cashAmount = (int) ($total - $billingAmount);
                $doctorShare = (int) floor($cashAmount / 2);
                $teamShare = (int) ($cashAmount - $doctorShare);
                break;
            }

            case 'Rawat Inap': {
                if (!in_array($serviceDetail, ['Reguler', 'VIP'], true)) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }
                $code = $serviceDetail === 'Reguler' ? 'RI_REG' : 'RI_VIP';
                $perHari = $this->regulationFixedPrice($code);
                $basePrice = $perHari;
                $total = $perHari * $qty;

                $finalPaymentType = 'billing';
                $finalLocation = self::RS_DEFAULT_LOCATION;
                break;
            }

            case 'Kematian': {
                if (!in_array($serviceDetail, ['Pemakaman', 'Kremasi'], true)) {
                    throw ValidationException::withMessages(['service_detail' => __('messages.medis_invalid_service_detail')]);
                }
                $code = $serviceDetail === 'Pemakaman' ? 'PEMAKAMAN' : 'KREMASI';
                $basePrice = $this->regulationFixedPrice($code);
                $total = $basePrice;

                if (!in_array($finalPaymentType, ['cash', 'billing'], true)) {
                    $finalPaymentType = 'cash';
                }
                break;
            }

            case 'Plastik': {
                $serviceDetail = 'Operasi Plastik';
                $finalOperasiTingkat = 'plastik';
                $finalLocation = self::RS_DEFAULT_LOCATION;
                $finalPaymentType = 'mixed';

                $cashAmount = 10140;
                $billingAmount = 10140;
                $basePrice = $cashAmount + $billingAmount;
                $total = $basePrice;
                break;
            }
        }

        if (in_array($serviceType, ['Pingsan', 'Treatment', 'Surat', 'Operasi', 'Rawat Inap', 'Plastik'], true) && $finalLocation === '') {
            $finalLocation = self::RS_DEFAULT_LOCATION;
        }

        // Finance columns for non-mixed types.
        if ($serviceType !== 'Operasi' && $serviceType !== 'Plastik') {
            if ($finalPaymentType === 'cash') {
                $cashAmount = $total;
                $billingAmount = 0;
            } elseif ($finalPaymentType === 'billing') {
                $billingAmount = $total;
                $cashAmount = 0;
            } else {
                // Normalize unexpected values.
                $finalPaymentType = 'cash';
                $cashAmount = $total;
                $billingAmount = 0;
            }
        }

        if ($total <= 0) {
            throw ValidationException::withMessages(['service_type' => __('messages.medis_total_not_calculated')]);
        }

        return [
            'service_type' => $serviceType,
            'service_detail' => $serviceDetail,
            'operasi_tingkat' => $finalOperasiTingkat,
            'patient_name' => $patientName !== '' ? $patientName : null,
            'location' => $finalLocation !== '' ? $finalLocation : null,
            'qty' => $qty,
            'payment_type' => $finalPaymentType,
            'price' => $basePrice,
            'total' => $total,
            'medicine_usage' => $medicineUsage,
            'billing_amount' => $billingAmount,
            'cash_amount' => $cashAmount,
            'doctor_share' => $doctorShare,
            'team_share' => $teamShare,
            'breakdown' => [
                'base_price' => $basePrice,
                'medicine' => [
                    'count' => count($areas),
                    'per_item' => $bleedingPerItem,
                    'code' => $bleedingCode,
                    'subtotal' => count($areas) * $bleedingPerItem,
                ],
            ],
        ];
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $positionNorm = Str::of((string) ($user['position'] ?? ''))->lower();
        if ($positionNorm->contains('trainee')) {
            abort(403);
        }

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        $roleNorm = Str::of((string) ($user['role'] ?? ''))->lower();
        $canShowAll = $roleNorm->contains('manager') || $roleNorm->contains('director');
        $showAll = $canShowAll && $request->boolean('show_all');

        $medicName = (string) ($user['name'] ?? '-');
        $medicJabatan = (string) ($user['position'] ?? '-');

        $query = EmsSale::query()->whereBetween('created_at', [$start, $end]);
        if (!$showAll) {
            $query->where('medic_name', $medicName);
        }

        $rows = $query
            ->orderByDesc('created_at')
            ->limit(250)
            ->get();

        $rekap = [
            'bandage' => 0,
            'p3k' => 0,
            'gauze' => 0,
            'iodine' => 0,
            'syringe' => 0,
            'billing' => 0,
            'cash' => 0,
            'total' => 0,
        ];

        foreach ($rows as $r) {
            $usage = (string) ($r->medicine_usage ?? '');
            $rekap['bandage'] += $this->countOccurrences($usage, 'BANDAGE');
            $rekap['p3k'] += $this->countOccurrences($usage, 'P3K');
            $rekap['gauze'] += $this->countOccurrences($usage, 'GAUZE');
            $rekap['iodine'] += $this->countOccurrences($usage, 'IODINE');
            $rekap['syringe'] += $this->countOccurrences($usage, 'SYRINGE');

            $total = (int) ($r->total ?? 0);
            $billing = (int) ($r->billing_amount ?? 0);
            $cash = (int) ($r->cash_amount ?? 0);

            if ($billing <= 0 && $cash <= 0) {
                $pt = Str::of((string) ($r->payment_type ?? ''))->lower()->toString();
                if ($pt === 'billing') $billing = $total;
                if ($pt === 'cash') $cash = $total;
            }

            $rekap['billing'] += max(0, $billing);
            $rekap['cash'] += max(0, $cash);
            $rekap['total'] += max(0, $total);
        }

        $priceBleedingNormal = 0;
        $priceBleedingPeluru = 0;
        try {
            $priceBleedingNormal = $this->regulationFixedPrice('BLEEDING_OBAT');
        } catch (\Throwable $e) {
            $priceBleedingNormal = 0;
        }
        try {
            $priceBleedingPeluru = $this->regulationFixedPrice('BLEEDING_PELURU');
        } catch (\Throwable $e) {
            $priceBleedingPeluru = 0;
        }

        return view('pages.layanan-medis', [
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'weeks' => $weeks,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'showAll' => $showAll,
            'canShowAll' => $canShowAll,
            'medicName' => $medicName,
            'medicJabatan' => $medicJabatan,
            'rows' => $rows,
            'rekap' => $rekap,
            'priceBleedingNormal' => $priceBleedingNormal,
            'priceBleedingPeluru' => $priceBleedingPeluru,
        ]);
    }

    public function previewPrice(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['success' => false, 'message' => __('messages.session_invalid_message')], 401);
        }

        $payload = $request->validate([
            'service_type' => ['required', 'string', 'max:50'],
            'service_detail' => ['nullable', 'string', 'max:100'],
            'operasi_tingkat' => ['nullable', 'string', 'max:10'],
            'patient_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'regex:/^\\d{1,4}$/'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:365'],
            'payment_type' => ['nullable', 'in:cash,billing,mixed'],
            'is_gunshot' => ['nullable'],
            'meds' => ['nullable', 'array'],
            'meds.*' => ['string'],
        ]);

        $payload['is_gunshot'] = $request->boolean('is_gunshot');

        try {
            $calc = $this->calculate($payload);
            return response()->json([
                'success' => true,
                'total' => (int) $calc['total'],
                'breakdown' => $calc['breakdown'],
                'payment_type' => (string) $calc['payment_type'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? __('messages.error'),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.error'),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return redirect()->route('login');
        }

        $positionNorm = Str::of((string) ($user['position'] ?? ''))->lower();
        if ($positionNorm->contains('trainee')) {
            abort(403);
        }

        $validated = $request->validate([
            'service_type' => ['required', 'string', 'max:50'],
            'service_detail' => ['nullable', 'string', 'max:100'],
            'operasi_tingkat' => ['nullable', 'string', 'max:10'],
            'patient_name' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'regex:/^\\d{1,4}$/'],
            'qty' => ['nullable', 'integer', 'min:1', 'max:365'],
            'payment_type' => ['nullable', 'in:cash,billing,mixed'],
            'is_gunshot' => ['nullable'],
            'meds' => ['nullable', 'array'],
            'meds.*' => ['string'],
        ], [
            'location.regex' => __('messages.medis_location_invalid'),
        ]);

        $validated['is_gunshot'] = $request->boolean('is_gunshot');

        $calc = $this->calculate($validated);

        EmsSale::query()->create([
            'service_type' => $calc['service_type'],
            'service_detail' => $calc['service_detail'],
            'operasi_tingkat' => $calc['operasi_tingkat'],
            'medicine_usage' => $calc['medicine_usage'],
            'patient_name' => $calc['patient_name'],
            'location' => $calc['location'],
            'qty' => $calc['qty'],
            'payment_type' => $calc['payment_type'],
            'price' => $calc['price'],
            'total' => $calc['total'],
            'medic_name' => (string) ($user['name'] ?? '-'),
            'medic_jabatan' => (string) ($user['position'] ?? '-'),
            'billing_amount' => $calc['billing_amount'],
            'cash_amount' => $calc['cash_amount'],
            'doctor_share' => $calc['doctor_share'],
            'team_share' => $calc['team_share'],
        ]);

        return back()->with('success', __('messages.medis_transaction_saved'));
    }

    public function destroy(EmsSale $sale)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['message' => __('messages.session_invalid_message')], 401);
        }

        $medicName = (string) ($user['name'] ?? '');
        if ($medicName === '' || (string) ($sale->medic_name ?? '') !== $medicName) {
            return response()->json(['message' => __('messages.medis_cannot_delete_other')], 403);
        }

        $sale->delete();
        return response()->noContent();
    }

    public function bulkDestroy(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['message' => __('messages.session_invalid_message')], 401);
        }

        $medicName = (string) ($user['name'] ?? '');
        if ($medicName === '') {
            return response()->json(['message' => __('messages.session_invalid_message')], 401);
        }

        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:250'],
            'ids.*' => ['integer', 'min:1'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'] ?? [])));
        if (empty($ids)) {
            return response()->json(['deleted' => 0]);
        }

        $deleted = EmsSale::query()
            ->whereIn('id', $ids)
            ->where('medic_name', $medicName)
            ->delete();

        return response()->json(['deleted' => (int) $deleted]);
    }
}
