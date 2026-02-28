<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RekapFarmasiController extends Controller
{
    private function titleCaseName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        $name = mb_strtolower($name);
        $parts = array_filter(explode(' ', $name), fn ($p) => $p !== '');
        $parts = array_map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)) . mb_substr($p, 1), $parts);
        return trim(implode(' ', $parts));
    }

    private function normalizeNameKey(string $name): string
    {
        return mb_strtolower(trim(preg_replace('/\s+/', ' ', $name)));
    }

    private function normalizeForCompare(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/[^a-z\s]/u', ' ', $name);
        $name = trim(preg_replace('/\s+/', ' ', $name));
        return (string) $name;
    }

    private function tokensFromName(string $name): array
    {
        $normalized = $this->normalizeForCompare($name);
        if ($normalized === '') {
            return [];
        }
        return array_values(array_filter(explode(' ', $normalized), fn ($t) => $t !== ''));
    }

    private function tokenSignature(array $tokens): string
    {
        $t = $tokens;
        sort($t);
        return implode('|', $t);
    }

    private function similarityScore(string $a, string $b): int
    {
        $na = $this->normalizeForCompare($a);
        $nb = $this->normalizeForCompare($b);
        if ($na === '' || $nb === '') {
            return 0;
        }
        if ($na === $nb) {
            return 100;
        }

        $ta = $this->tokensFromName($na);
        $tb = $this->tokensFromName($nb);

        if (!empty($ta) && !empty($tb)) {
            if ($this->tokenSignature($ta) === $this->tokenSignature($tb)) {
                return 95; // same tokens, different order
            }

            $common = array_values(array_intersect($ta, $tb));
            $commonCount = count($common);
            $minTokens = min(count($ta), count($tb));

            // Strong match: contains both first+last tokens somewhere
            if ($minTokens >= 2) {
                $fa = $ta[0];
                $la = $ta[count($ta) - 1];
                $hasFirst = in_array($fa, $tb, true);
                $hasLast = in_array($la, $tb, true);
                if ($hasFirst && $hasLast) {
                    return 90;
                }

                // Fuzzy first token, exact last token
                $fb = $tb[0];
                $lb = $tb[count($tb) - 1];
                if ($la === $lb) {
                    $dist = levenshtein($fa, $fb);
                    if ($dist <= 2) {
                        return 88;
                    }
                }
                if ($fa === $fb) {
                    $dist = levenshtein($la, $lb);
                    if ($dist <= 2) {
                        return 88;
                    }
                }
            }

            if ($commonCount >= 2) {
                return 85;
            }

            if ($commonCount === 1 && $minTokens >= 2) {
                // weaker, but still potentially suspicious
                return 70;
            }
        }

        $maxLen = max(mb_strlen($na), mb_strlen($nb));
        $dist = levenshtein($na, $nb);
        $ratio = $maxLen > 0 ? (1 - ($dist / $maxLen)) : 0;
        return (int) round(max(0, min(84, $ratio * 84)));
    }

    private function findSimilarConsumerNamesForDate(string $inputName, Carbon $date, int $limit = 50): array
    {
        $tokens = $this->tokensFromName($inputName);
        if (count($tokens) < 1) {
            return [];
        }

        $first = $tokens[0] ?? '';
        $last = $tokens[count($tokens) - 1] ?? '';

        $query = Sale::query()
            ->whereDate('created_at', $date)
            ->selectRaw('DISTINCT consumer_name');

        if ($first !== '' && $last !== '' && $first !== $last) {
            $query->where(function ($q) use ($first, $last) {
                $q->whereRaw('LOWER(consumer_name) LIKE ?', ['%' . $first . '%'])
                  ->orWhereRaw('LOWER(consumer_name) LIKE ?', ['%' . $last . '%']);
            });
        } elseif ($first !== '') {
            $query->whereRaw('LOWER(consumer_name) LIKE ?', ['%' . $first . '%']);
        }

        $names = $query->limit(300)->pluck('consumer_name')->all();

        $results = [];
        $inputNorm = $this->normalizeForCompare($inputName);
        foreach ($names as $name) {
            if ($inputNorm !== '' && $this->normalizeForCompare((string) $name) === $inputNorm) {
                continue;
            }
            $score = $this->similarityScore($inputName, (string) $name);
            if ($score >= 85) {
                $results[] = ['name' => (string) $name, 'score' => $score];
            }
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $limit);
    }

    private function findSimilarConsumerNamesAllTime(string $inputName, int $limit = 10): array
    {
        $tokens = $this->tokensFromName($inputName);
        if (count($tokens) < 1) {
            return [];
        }

        $first = $tokens[0] ?? '';
        $last = $tokens[count($tokens) - 1] ?? '';

        $query = Sale::query()
            ->selectRaw('DISTINCT consumer_name');

        if ($first !== '' && $last !== '' && $first !== $last) {
            $query->where(function ($q) use ($first, $last) {
                $q->whereRaw('LOWER(consumer_name) LIKE ?', ['%' . $first . '%'])
                    ->orWhereRaw('LOWER(consumer_name) LIKE ?', ['%' . $last . '%']);
            });
        } elseif ($first !== '') {
            $query->whereRaw('LOWER(consumer_name) LIKE ?', ['%' . $first . '%']);
        }

        $names = $query->limit(400)->pluck('consumer_name')->all();

        $results = [];
        $inputNorm = $this->normalizeForCompare($inputName);
        foreach ($names as $name) {
            if ($inputNorm !== '' && $this->normalizeForCompare((string) $name) === $inputNorm) {
                continue;
            }
            $score = $this->similarityScore($inputName, (string) $name);
            if ($score >= 85) {
                $results[] = ['name' => (string) $name, 'score' => $score];
            }
        }

        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($results, 0, $limit);
    }

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

        $rangeLabel = $start
            ->copy()
            ->locale(app()->getLocale())
            ->translatedFormat('d M Y')
            . ' - ' .
            $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y');

        return [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput];
    }

    private function getUnitPrices(): array
    {
        $unit = [
            'bandage' => 0,
            'ifaks' => 0,
            'painkiller' => 0,
        ];

        $bandage = Package::query()
            ->where('bandage_qty', '>', 0)
            ->where('ifaks_qty', '=', 0)
            ->where('painkiller_qty', '=', 0)
            ->orderByDesc('bandage_qty')
            ->first();
        if ($bandage) {
            $unit['bandage'] = (int) ((int) $bandage->price / max(1, (int) $bandage->bandage_qty));
        }

        $ifaks = Package::query()
            ->where('ifaks_qty', '>', 0)
            ->where('bandage_qty', '=', 0)
            ->where('painkiller_qty', '=', 0)
            ->orderByDesc('ifaks_qty')
            ->first();
        if ($ifaks) {
            $unit['ifaks'] = (int) ((int) $ifaks->price / max(1, (int) $ifaks->ifaks_qty));
        }

        $painkiller = Package::query()
            ->where('painkiller_qty', '>', 0)
            ->where('bandage_qty', '=', 0)
            ->where('ifaks_qty', '=', 0)
            ->orderByDesc('painkiller_qty')
            ->first();
        if ($painkiller) {
            $unit['painkiller'] = (int) ((int) $painkiller->price / max(1, (int) $painkiller->painkiller_qty));
        }

        return $unit;
    }

    private function buildSingleItemPackageOptions(string $item): array
    {
        $q = Package::query();

        if ($item === 'bandage') {
            $q->where('bandage_qty', '>', 0)->where('ifaks_qty', '=', 0)->where('painkiller_qty', '=', 0);
        } elseif ($item === 'ifaks') {
            $q->where('ifaks_qty', '>', 0)->where('bandage_qty', '=', 0)->where('painkiller_qty', '=', 0);
        } else {
            $q->where('painkiller_qty', '>', 0)->where('bandage_qty', '=', 0)->where('ifaks_qty', '=', 0);
        }

        $rows = $q->orderBy('id')->get();

        $options = ['' => __('messages.none')];
        $map = [];

        foreach ($rows as $p) {
            $qty = $item === 'bandage'
                ? (int) $p->bandage_qty
                : ($item === 'ifaks' ? (int) $p->ifaks_qty : (int) $p->painkiller_qty);
            $price = (int) $p->price;
            $unit = (int) ($price / max(1, $qty));

            $label = strtoupper((string) $p->name) . ' ($ ' . number_format($price) . ')';

            $options[(string) $p->id] = $label;
            $map[(string) $p->id] = [
                'id' => (int) $p->id,
                'name' => (string) $p->name,
                'qty' => $qty,
                'price' => $price,
                'unit_price' => $unit,
                'item' => $item,
            ];
        }

        return [$options, $map];
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        $positionNorm = Str::of((string) ($user['position'] ?? ''))->lower();
        if ($positionNorm->contains('trainee')) {
            abort(403);
        }

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        $showAll = $request->boolean('show_all');
        $salesQuery = Sale::query()->whereBetween('created_at', [$start, $end]);
        if (!$showAll && !empty($user['id'])) {
            $salesQuery->where('medic_user_id', (int) $user['id']);
        }

        $sales = $salesQuery
            ->orderByDesc('created_at')
            ->limit(250)
            ->get();

        $todayStart = Carbon::now()->startOfDay();
        $todayEnd = Carbon::now()->endOfDay();
        $todayStats = null;
        if (!empty($user['id'])) {
            $todayStats = Sale::query()
                ->where('medic_user_id', (int) $user['id'])
                ->whereBetween('created_at', [$todayStart, $todayEnd])
                ->selectRaw('COUNT(*) AS total_transaksi')
                ->selectRaw('COALESCE(SUM(price), 0) AS total_harga')
                ->first();
        }

        $pkgA = Package::query()
            ->whereRaw('UPPER(name) LIKE ?', ['PAKET A%'])
            ->orderBy('id')
            ->first();
        $pkgB = Package::query()
            ->whereRaw('UPPER(name) LIKE ?', ['PAKET B%'])
            ->orderBy('id')
            ->first();

        $unitPrices = $this->getUnitPrices();

        [$bandageOptions, $bandageMap] = $this->buildSingleItemPackageOptions('bandage');
        [$ifaksOptions, $ifaksMap] = $this->buildSingleItemPackageOptions('ifaks');
        [$painkillerOptions, $painkillerMap] = $this->buildSingleItemPackageOptions('painkiller');
        // Preserve package IDs as keys (avoid numeric reindexing).
        $customPackageMap = $bandageMap + $ifaksMap + $painkillerMap;

        return view('pages.rekap-farmasi', [
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'weeks' => $weeks,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'showAll' => $showAll,
            'sales' => $sales,
            'todayStats' => $todayStats,
            'pkgA' => $pkgA,
            'pkgB' => $pkgB,
            'unitPrices' => $unitPrices,
            'bandageOptions' => $bandageOptions,
            'ifaksOptions' => $ifaksOptions,
            'painkillerOptions' => $painkillerOptions,
            'customPackageMap' => $customPackageMap,
        ]);
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
            'consumer_name' => ['required', 'string', 'min:2', 'max:100'],
            'package_type' => ['required', 'in:paket_a,paket_b,paket_custom'],
            'custom_bandage_package_id' => ['nullable', 'integer', 'min:1'],
            'custom_ifaks_package_id' => ['nullable', 'integer', 'min:1'],
            'custom_painkiller_package_id' => ['nullable', 'integer', 'min:1'],
            'auto_merge' => ['nullable', 'in:0,1'],
            'merge_targets' => ['nullable', 'string'],
        ]);

        $consumerName = $this->titleCaseName((string) $validated['consumer_name']);
        $consumerKey = $this->normalizeNameKey($consumerName);

        $today = Carbon::today();

        $mergeTargets = [];
        if (($validated['auto_merge'] ?? null) === '1') {
            $decoded = json_decode((string) ($validated['merge_targets'] ?? '[]'), true);
            if (is_array($decoded)) {
                $mergeTargets = array_values(array_filter($decoded, fn ($v) => is_string($v) && trim($v) !== ''));
            }
        }

        if (!empty($mergeTargets)) {
            $similar = $this->findSimilarConsumerNamesAllTime($consumerName, 50);
            $allowed = array_map(fn ($r) => $r['name'], $similar);
            $targets = array_values(array_intersect($mergeTargets, $allowed));

            foreach ($targets as $old) {
                Sale::query()
                    ->whereRaw('LOWER(consumer_name) = ?', [mb_strtolower($old)])
                    ->update(['consumer_name' => $consumerName]);
            }
        }

        $already = Sale::query()
            ->whereRaw('LOWER(consumer_name) = ?', [$consumerKey])
            ->whereDate('created_at', $today)
            ->exists();
        if (!$already) {
            // Prevent bypass by slight name variations on the same date (WIB date boundary).
            $similarToday = $this->findSimilarConsumerNamesForDate($consumerName, $today, 5);
            if (!empty($similarToday)) {
                $already = true;
            }
        }
        if ($already) {
            return back()
                ->withInput()
                ->withErrors(['consumer_name' => __('messages.farmasi_consumer_already_transacted_today')]);
        }

        $packageType = (string) $validated['package_type'];

        $packageId = 0;
        $packageName = '';
        $price = 0;
        $qtyBandage = 0;
        $qtyIfaks = 0;
        $qtyPain = 0;

        if ($packageType === 'paket_a' || $packageType === 'paket_b') {
            $pkg = Package::query()
                ->whereRaw('UPPER(name) LIKE ?', [$packageType === 'paket_a' ? 'PAKET A%' : 'PAKET B%'])
                ->orderBy('id')
                ->first();

            if (!$pkg) {
                return back()
                    ->withInput()
                    ->withErrors(['package_type' => __('messages.farmasi_package_not_found')]);
            }

            $packageId = (int) $pkg->id;
            $packageName = (string) $pkg->name;
            $price = (int) $pkg->price;
            $qtyBandage = (int) $pkg->bandage_qty;
            $qtyIfaks = (int) $pkg->ifaks_qty;
            $qtyPain = (int) $pkg->painkiller_qty;
        } else {
            $bandageId = (int) ($validated['custom_bandage_package_id'] ?? 0);
            $ifaksId = (int) ($validated['custom_ifaks_package_id'] ?? 0);
            $painId = (int) ($validated['custom_painkiller_package_id'] ?? 0);

            $selectedIds = array_values(array_unique(array_filter([$bandageId, $ifaksId, $painId], fn ($id) => $id > 0)));

            if (count($selectedIds) <= 0) {
                return back()
                    ->withInput()
                    ->withErrors(['package_type' => __('messages.farmasi_custom_min_one')]);
            }

            $packages = Package::query()->whereIn('id', $selectedIds)->get()->keyBy('id');
            foreach ($selectedIds as $id) {
                if (!isset($packages[$id])) {
                    return back()
                        ->withInput()
                        ->withErrors(['package_type' => __('messages.farmasi_package_not_found')]);
                }
            }

            $price = 0;
            $notes = [];

            foreach ($selectedIds as $id) {
                $p = $packages[$id];
                $price += (int) $p->price;

                $b = (int) $p->bandage_qty;
                $i = (int) $p->ifaks_qty;
                $pa = (int) $p->painkiller_qty;

                if ($b > 0 && $i === 0 && $pa === 0) {
                    $qtyBandage += $b;
                } elseif ($i > 0 && $b === 0 && $pa === 0) {
                    $qtyIfaks += $i;
                } elseif ($pa > 0 && $b === 0 && $i === 0) {
                    $qtyPain += $pa;
                } else {
                    return back()
                        ->withInput()
                        ->withErrors(['package_type' => __('messages.farmasi_package_not_found')]);
                }

                $notes[] = (string) $p->name;
            }

            // `sales.package_id` has a FK to `packages.id`, so Paket Custom must reference a real package id.
            // Use the first selected package as FK anchor, while keeping `package_name` as "Paket Custom".
            $packageId = (int) ($selectedIds[0] ?? 0);
            $packageName = 'Paket Custom';
            $keterangan = implode(' + ', $notes);
        }

        $txHash = hash('sha256', Str::uuid()->toString() . '|' . microtime(true));

        Sale::query()->create([
            'consumer_name' => $consumerName,
            'consumer_id' => null,
            'medic_name' => (string) ($user['name'] ?? '-'),
            'medic_user_id' => (int) $user['id'],
            'medic_jabatan' => (string) ($user['position'] ?? '-'),
            'package_id' => $packageId,
            'package_name' => $packageName,
            'price' => $price,
            'qty_bandage' => $qtyBandage,
            'qty_ifaks' => $qtyIfaks,
            'qty_painkiller' => $qtyPain,
            'keterangan' => $keterangan ?? null,
            'created_at' => Carbon::now(),
            'tx_hash' => $txHash,
            'identity_id' => null,
            'synced_to_sheet' => false,
        ]);

        return redirect()
            ->route('farmasi.rekap', $request->only(['range', 'from', 'to', 'show_all']))
            ->with('success', __('messages.farmasi_transaction_saved'));
    }

    public function checkConsumerToday(Request $request)
    {
        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['already' => false, 'similar' => [], 'merge_targets' => []]);
        }

        $name = (string) $request->query('name', '');
        $name = $this->titleCaseName($name);
        if (mb_strlen($name) < 2) {
            return response()->json(['already' => false, 'similar' => [], 'merge_targets' => []]);
        }

        $key = $this->normalizeNameKey($name);
        $today = Carbon::today();

        $similarAll = $this->findSimilarConsumerNamesAllTime($name, 10);
        $mergeTargets = array_map(fn ($r) => $r['name'], $similarAll);

        $alreadyExact = Sale::query()
            ->whereRaw('LOWER(consumer_name) = ?', [$key])
            ->whereDate('created_at', $today)
            ->exists();

        $similarToday = $this->findSimilarConsumerNamesForDate($name, $today, 10);
        $already = $alreadyExact || !empty($similarToday);

        return response()->json([
            'already' => $already,
            'already_exact' => $alreadyExact,
            'similar' => $similarAll,
            'similar_today' => $similarToday,
            'merge_targets' => $mergeTargets,
        ]);
    }

    public function mergeSimilar(Request $request)
    {
        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['success' => false], 401);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'targets' => ['nullable', 'array'],
            'targets.*' => ['string'],
        ]);

        $name = $this->titleCaseName((string) $validated['name']);
        $similar = $this->findSimilarConsumerNamesAllTime($name, 50);
        $allowed = array_map(fn ($r) => $r['name'], $similar);
        $targets = array_values(array_intersect((array) ($validated['targets'] ?? []), $allowed));

        $mergedRows = 0;
        foreach ($targets as $old) {
            $mergedRows += Sale::query()
                ->whereRaw('LOWER(consumer_name) = ?', [mb_strtolower($old)])
                ->update(['consumer_name' => $name]);
        }

        $already = Sale::query()
            ->whereRaw('LOWER(consumer_name) = ?', [$this->normalizeNameKey($name)])
            ->whereDate('created_at', Carbon::today())
            ->exists();

        return response()->json([
            'success' => true,
            'merged_rows' => $mergedRows,
            'already_today' => $already,
        ]);
    }

    public function searchConsumers(Request $request)
    {
        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['results' => []], 401);
        }

        $q = (string) $request->query('q', '');
        $q = trim($q);
        if (mb_strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $needle = mb_strtolower($q);

        $rows = Sale::query()
            ->select('consumer_name')
            ->selectRaw('COUNT(*) AS total_transactions')
            ->selectRaw('COALESCE(SUM(qty_bandage), 0) AS total_bandage')
            ->selectRaw('COALESCE(SUM(qty_ifaks), 0) AS total_ifaks')
            ->selectRaw('COALESCE(SUM(qty_painkiller), 0) AS total_painkiller')
            ->selectRaw('MAX(created_at) AS last_purchase_at')
            ->whereRaw('LOWER(consumer_name) LIKE ?', ['%' . $needle . '%'])
            ->groupBy('consumer_name')
            ->orderByDesc(DB::raw('last_purchase_at'))
            ->limit(10)
            ->get();

        $results = $rows->map(function ($r) {
            $dt = $r->last_purchase_at ? Carbon::parse($r->last_purchase_at) : null;

            return [
                'name' => (string) $r->consumer_name,
                'total_transactions' => (int) ($r->total_transactions ?? 0),
                'total_bandage' => (int) ($r->total_bandage ?? 0),
                'total_ifaks' => (int) ($r->total_ifaks ?? 0),
                'total_painkiller' => (int) ($r->total_painkiller ?? 0),
                'last_purchase_iso' => $dt?->toIso8601String(),
                'last_purchase_id' => $dt?->copy()->locale('id')->translatedFormat('d M Y H:i'),
                'last_purchase_en' => $dt?->copy()->locale('en')->translatedFormat('d M Y H:i'),
            ];
        })->values();

        return response()->json(['results' => $results]);
    }
}
