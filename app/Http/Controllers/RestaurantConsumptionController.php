<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RestaurantConsumptionController extends Controller
{
    private const SESSION_KEY_ROWS = 'restaurant_consumptions_ui';

    private function getRestaurantsFromDb(): array
    {
        return DB::table('restaurant_settings')
            ->orderBy('restaurant_name', 'asc')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();
    }

    private function getConsumptionRows(Request $request): array
    {
        $rows = $request->session()->get(self::SESSION_KEY_ROWS, []);
        return is_array($rows) ? $rows : [];
    }

    private function putConsumptionRows(Request $request, array $rows): void
    {
        $request->session()->put(self::SESSION_KEY_ROWS, array_values($rows));
    }

    private function resolveRange(Request $request): array
    {
        $validRanges = ['week1', 'week2', 'week3', 'week4', 'month1', 'custom', 'all'];
        $range = (string) $request->query('range', 'week4');
        if (!in_array($range, $validRanges, true)) {
            $range = 'week4';
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
            'month1' => [
                'start' => $now->copy()->startOfMonth()->startOfDay(),
                'end' => $now->copy()->endOfMonth()->endOfDay(),
                'label' => __('messages.range_this_month'),
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
            case 'month1':
            default:
                $start = $weeks[$range]['start'] ?? $weeks['week4']['start'];
                $end = $weeks[$range]['end'] ?? $weeks['week4']['end'];
                break;
        }

        $rangeLabel = $range === 'all'
            ? __('messages.range_all')
            : ($start && $end
                ? ($start->copy()->locale(app()->getLocale())->translatedFormat('d M Y') . ' – ' . $end->copy()->locale(app()->getLocale())->translatedFormat('d M Y'))
                : '-');

        return [$range, $start, $end, (string) $rangeLabel, $weeks, $fromInput, $toInput];
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
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $restaurants = $this->getRestaurantsFromDb();

        $user = $this->userUi();
        $userId = (int) ($user['id'] ?? 0);
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isDirector = in_array($userRole, ['vice director', 'director'], true);
        $canManage = !in_array($userRole, ['staff', 'manager'], true);
        $isStaff = $userRole === 'staff';

        [$range, $start, $end, $rangeLabel, $weeks, $fromInput, $toInput] = $this->resolveRange($request);

        $rows = $this->getConsumptionRows($request);

        if ($isStaff) {
            $rows = array_values(array_filter($rows, fn ($r) => (int) ($r['created_by'] ?? 0) === $userId));
        }

        if ($range !== 'all' && $start && $end) {
            $rows = array_values(array_filter($rows, function ($r) use ($start, $end) {
                $dt = (string) ($r['delivery_at'] ?? '');
                if ($dt === '') {
                    return false;
                }
                try {
                    $c = Carbon::parse($dt);
                } catch (\Throwable $e) {
                    return false;
                }
                return $c->between($start, $end, true);
            }));
        }

        usort($rows, function ($a, $b) {
            $da = (string) ($a['delivery_at'] ?? '');
            $db = (string) ($b['delivery_at'] ?? '');
            return strcmp($db, $da);
        });

        $stats = array_reduce($rows, function ($acc, $r) {
            $acc['total_packets'] += (int) ($r['packet_count'] ?? 0);
            $acc['total_subtotal'] += (float) ($r['subtotal'] ?? 0);
            $acc['total_tax'] += (float) ($r['tax_amount'] ?? 0);
            $acc['total_grand'] += (float) ($r['total_amount'] ?? 0);
            return $acc;
        }, ['total_packets' => 0, 'total_subtotal' => 0, 'total_tax' => 0, 'total_grand' => 0]);

        return view('pages.farmasi.restaurant-consumption', [
            'restaurants' => $restaurants,
            'rows' => $rows,
            'stats' => $stats,
            'range' => $range,
            'weeks' => $weeks,
            'rangeLabel' => $rangeLabel,
            'fromInput' => $fromInput,
            'toInput' => $toInput,
            'userRole' => $userRole,
            'isDirector' => $isDirector,
            'canManage' => $canManage,
            'defaultCode' => 'CONS-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(3)),
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
        $userName = (string) ($user['name'] ?? $user['full_name'] ?? '');

        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer', 'min:1'],
            'delivery_date' => ['required', 'date'],
            'delivery_time' => ['required', 'date_format:H:i'],
            'packet_count' => ['required', 'integer', 'min:1', 'max:9999'],
            'notes' => ['nullable', 'string', 'max:500'],
            'ktp_file' => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
        ]);

        $restaurantId = (int) $validated['restaurant_id'];
        $restaurant = DB::table('restaurant_settings')
            ->where('id', $restaurantId)
            ->where('is_active', 1)
            ->first();

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => __('messages.restaurant_consumption_restaurant_inactive'),
            ], 422);
        }

        $deliveryAt = Carbon::parse((string) $validated['delivery_date'] . ' ' . (string) $validated['delivery_time'])
            ->seconds(0);

        $packetCount = (int) $validated['packet_count'];
        $pricePerPacket = (float) ($restaurant->price_per_packet ?? 0);
        $taxPercentage = (float) ($restaurant->tax_percentage ?? 0);
        $subtotal = $packetCount * $pricePerPacket;
        $taxAmount = $subtotal * ($taxPercentage / 100);
        $totalAmount = $subtotal + $taxAmount;

        $code = 'CONS-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(3));

        $ktpPath = null;
        $file = $request->file('ktp_file');
        if ($file && $file->isValid()) {
            $relative = 'restaurant_consumptions/ktp/' . $code . '.jpg';
            $target = Storage::disk('public')->path($relative);
            $dir = dirname($target);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            $ok = $this->compressImageSmart($file->getPathname(), $target, 1200, 300000);
            if ($ok && file_exists($target)) {
                $ktpPath = 'storage/' . $relative;
            } else {
                $stored = $file->storeAs('restaurant_consumptions/ktp', $code . '.' . strtolower((string) $file->getClientOriginalExtension() ?: 'jpg'), 'public');
                $ktpPath = 'storage/' . ltrim((string) $stored, '/');
            }
        }

        $rows = $this->getConsumptionRows($request);
        $nextId = 1;
        foreach ($rows as $r) {
            $nextId = max($nextId, ((int) ($r['id'] ?? 0)) + 1);
        }

        $rows[] = [
            'id' => $nextId,
            'consumption_code' => $code,
            'delivery_at' => $deliveryAt->toIso8601String(),
            'restaurant_id' => $restaurantId,
            'restaurant_name' => (string) ($restaurant->restaurant_name ?? '-'),
            'recipient_name' => $userName !== '' ? $userName : '-',
            'created_by' => $userId,
            'created_by_name' => $userName !== '' ? $userName : '-',
            'packet_count' => $packetCount,
            'price_per_packet' => $pricePerPacket,
            'subtotal' => round($subtotal, 2),
            'tax_percentage' => $taxPercentage,
            'tax_amount' => round($taxAmount, 2),
            'total_amount' => round($totalAmount, 2),
            'ktp_file' => $ktpPath,
            'notes' => (string) ($validated['notes'] ?? ''),
            'status' => 'pending',
            'approved_by' => null,
            'approved_by_name' => null,
            'approved_at' => null,
            'paid_by' => null,
            'paid_by_name' => null,
            'paid_at' => null,
            'created_at' => now()->toIso8601String(),
        ];

        $this->putConsumptionRows($request, $rows);

        return response()->json([
            'success' => true,
            'message' => __('messages.restaurant_consumption_saved'),
        ]);
    }

    public function approve(Request $request, int $id)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $user = $this->userUi();
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $canManage = !in_array($userRole, ['staff', 'manager'], true);
        if (!$canManage) {
            abort(403);
        }

        $rows = $this->getConsumptionRows($request);
        $found = false;
        foreach ($rows as &$r) {
            if ((int) ($r['id'] ?? 0) !== $id) {
                continue;
            }
            if ((string) ($r['status'] ?? '') !== 'pending') {
                break;
            }
            $r['status'] = 'approved';
            $r['approved_by'] = (int) ($user['id'] ?? 0);
            $r['approved_by_name'] = (string) ($user['name'] ?? $user['full_name'] ?? '-');
            $r['approved_at'] = now()->toIso8601String();
            $found = true;
            break;
        }
        unset($r);

        if (!$found) {
            return response()->json(['success' => false, 'message' => __('messages.restaurant_consumption_not_found')], 404);
        }

        $this->putConsumptionRows($request, $rows);

        return response()->json(['success' => true, 'message' => __('messages.restaurant_consumption_approved')]);
    }

    public function paid(Request $request, int $id)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $user = $this->userUi();
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $canManage = !in_array($userRole, ['staff', 'manager'], true);
        if (!$canManage) {
            abort(403);
        }

        $rows = $this->getConsumptionRows($request);
        $found = false;
        foreach ($rows as &$r) {
            if ((int) ($r['id'] ?? 0) !== $id) {
                continue;
            }
            if ((string) ($r['status'] ?? '') !== 'approved') {
                break;
            }
            $r['status'] = 'paid';
            $r['paid_by'] = (int) ($user['id'] ?? 0);
            $r['paid_by_name'] = (string) ($user['name'] ?? $user['full_name'] ?? '-');
            $r['paid_at'] = now()->toIso8601String();
            $found = true;
            break;
        }
        unset($r);

        if (!$found) {
            return response()->json(['success' => false, 'message' => __('messages.restaurant_consumption_not_found')], 404);
        }

        $this->putConsumptionRows($request, $rows);

        return response()->json(['success' => true, 'message' => __('messages.restaurant_consumption_paid')]);
    }

    public function destroy(Request $request, int $id)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $user = $this->userUi();
        $userRole = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        $isDirector = in_array($userRole, ['vice director', 'director'], true);
        if (!$isDirector) {
            abort(403);
        }

        $rows = $this->getConsumptionRows($request);
        $before = count($rows);
        $rows = array_values(array_filter($rows, fn ($r) => (int) ($r['id'] ?? 0) !== $id));

        if (count($rows) === $before) {
            return response()->json(['success' => false, 'message' => __('messages.restaurant_consumption_not_found')], 404);
        }

        $this->putConsumptionRows($request, $rows);

        return response()->json(['success' => true, 'message' => __('messages.deleted')]);
    }
}
