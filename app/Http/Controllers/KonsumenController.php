<?php

namespace App\Http\Controllers;

use App\Models\IdentityMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KonsumenController extends Controller
{
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

        $q = trim((string) $request->query('q', ''));
        $consumer = trim((string) $request->query('consumer', ''));
        $medic = trim((string) $request->query('medic', ''));

        $hasSearch = (mb_strlen($consumer) >= 2) || (mb_strlen($medic) >= 2) || (mb_strlen($q) >= 2);

        $rows = collect();
        if ($hasSearch) {
            $query = DB::table('sales as s')
                ->leftJoin('identity_master as im', 'im.id', '=', 's.identity_id')
                ->whereBetween('s.created_at', [$start, $end]);

            if ($consumer !== '') {
                $query->where('s.consumer_name', 'like', '%' . $consumer . '%');
            }
            if ($medic !== '') {
                $query->where('s.medic_name', 'like', '%' . $medic . '%');
            }
            if ($q !== '') {
                $query->where(function ($w) use ($q) {
                    $like = '%' . $q . '%';
                    $w->where('s.consumer_name', 'like', $like)
                        ->orWhere('im.citizen_id', 'like', $like)
                        ->orWhere('s.medic_name', 'like', $like);
                });
            }

            $rows = $query
                ->select([
                    's.id',
                    's.created_at',
                    's.consumer_name',
                    's.medic_name',
                    's.medic_jabatan',
                    's.qty_bandage',
                    's.qty_ifaks',
                    's.qty_painkiller',
                    's.price',
                    's.identity_id',
                    'im.citizen_id',
                ])
                ->orderByDesc('s.created_at')
                ->limit(250)
                ->get();
        }

        $totals = [
            'bandage' => 0,
            'ifaks' => 0,
            'painkiller' => 0,
            'items' => 0,
            'price' => 0,
        ];

        foreach ($rows as $r) {
            $b = (int) ($r->qty_bandage ?? 0);
            $i = (int) ($r->qty_ifaks ?? 0);
            $p = (int) ($r->qty_painkiller ?? 0);
            $totals['bandage'] += $b;
            $totals['ifaks'] += $i;
            $totals['painkiller'] += $p;
            $totals['items'] += ($b + $i + $p);
            $totals['price'] += (int) ($r->price ?? 0);
        }

        return view('pages.farmasi.konsumen.index', [
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'weeks' => $weeks,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'q' => $q,
            'consumer' => $consumer,
            'medic' => $medic,
            'hasSearch' => $hasSearch,
            'rows' => $rows,
            'totals' => $totals,
        ]);
    }

    public function identityJson(IdentityMaster $identity)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        $user = session('user');
        if (empty($user['id'])) {
            return response()->json(['message' => __('messages.session_invalid_message')], 401);
        }

        $imagePath = (string) ($identity->image_path ?? '');
        $imageUrl = null;
        if ($imagePath !== '') {
            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                $imageUrl = $imagePath;
            } elseif (Str::startsWith($imagePath, ['storage/', '/storage/'])) {
                $imageUrl = asset(ltrim($imagePath, '/'));
            } else {
                $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
            }
        }

        return response()->json([
            'id' => (int) $identity->id,
            'citizen_id' => (string) ($identity->citizen_id ?? ''),
            'first_name' => (string) ($identity->first_name ?? ''),
            'last_name' => (string) ($identity->last_name ?? ''),
            'full_name' => trim((string) ($identity->first_name ?? '') . ' ' . (string) ($identity->last_name ?? '')),
            'dob' => $identity->dob?->toDateString(),
            'sex' => (string) ($identity->sex ?? ''),
            'nationality' => (string) ($identity->nationality ?? ''),
            'image_url' => $imageUrl,
        ]);
    }
}
