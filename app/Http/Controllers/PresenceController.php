<?php

namespace App\Http\Controllers;

use App\Models\UserFarmasiStatus;
use Illuminate\Http\Request;

class PresenceController extends Controller
{
    public function ping(Request $request)
    {
        $userId = (int) session('user.id', 0);
        if ($userId <= 0) {
            return response()->json(['ok' => false], 401);
        }

        $now = now();
        $ttlSeconds = 45; // presence TTL (short), separate from duty window

        UserFarmasiStatus::query()->updateOrInsert(
            ['user_id' => $userId],
            [
                'status' => 'online',
                'last_activity_at' => $now,
                'last_confirm_at' => $now,
                'auto_offline_at' => $now->copy()->addSeconds($ttlSeconds),
                'updated_at' => $now,
            ]
        );

        return response()->json([
            'ok' => true,
            'auto_offline_at' => $now->copy()->addSeconds($ttlSeconds)->toIso8601String(),
        ]);
    }

    public function offline(Request $request)
    {
        $userId = (int) session('user.id', 0);
        if ($userId <= 0) {
            return response()->json(['ok' => false], 401);
        }

        $now = now();

        UserFarmasiStatus::query()->updateOrInsert(
            ['user_id' => $userId],
            [
                'status' => 'offline',
                'last_activity_at' => $now,
                'last_confirm_at' => $now,
                'auto_offline_at' => $now,
                'updated_at' => $now,
            ]
        );

        return response()->json(['ok' => true]);
    }
}

