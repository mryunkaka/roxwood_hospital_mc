<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestaurantSettingsController extends Controller
{
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

    private function assertCanManage(): void
    {
        $user = $this->userUi();
        $role = Str::of((string) ($user['role'] ?? ''))->lower()->trim()->toString();
        if (in_array($role, ['staff', 'manager'], true)) {
            abort(403);
        }
    }

    private function normalizeName(string $name): string
    {
        $name = trim(preg_replace('/\s+/', ' ', $name));
        return $name;
    }

    public function index(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $this->assertCanManage();

        $restaurants = DB::table('restaurant_settings')
            ->orderBy('restaurant_name', 'asc')
            ->get()
            ->map(fn ($r) => (array) $r)
            ->all();

        return view('pages.farmasi.restaurant-settings', [
            'restaurants' => $restaurants,
        ]);
    }

    public function store(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $this->assertCanManage();

        $validated = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:120'],
            'price_per_packet' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'in:1'],
        ]);

        DB::table('restaurant_settings')->insert([
            'restaurant_name' => $this->normalizeName((string) $validated['restaurant_name']),
            'price_per_packet' => (float) $validated['price_per_packet'],
            'tax_percentage' => (float) $validated['tax_percentage'],
            'is_active' => isset($validated['is_active']) ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', __('messages.restaurant_settings_created'));
    }

    public function update(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $this->assertCanManage();

        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'restaurant_name' => ['required', 'string', 'max:120'],
            'price_per_packet' => ['required', 'numeric', 'min:0', 'max:999999999999'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'in:1'],
        ]);

        $id = (int) $validated['id'];

        $updated = DB::table('restaurant_settings')
            ->where('id', $id)
            ->update([
                'restaurant_name' => $this->normalizeName((string) $validated['restaurant_name']),
                'price_per_packet' => (float) $validated['price_per_packet'],
                'tax_percentage' => (float) $validated['tax_percentage'],
                'is_active' => isset($validated['is_active']) ? 1 : 0,
                'updated_at' => now(),
            ]);

        if ($updated <= 0) {
            return back()->with('error', __('messages.restaurant_settings_not_found'));
        }

        return back()->with('success', __('messages.restaurant_settings_updated'));
    }

    public function toggle(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $this->assertCanManage();

        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'is_active' => ['required', 'in:0,1'],
        ]);

        $id = (int) $validated['id'];
        $active = (int) $validated['is_active'];

        $updated = DB::table('restaurant_settings')
            ->where('id', $id)
            ->update([
                'is_active' => $active,
                'updated_at' => now(),
            ]);

        if ($updated <= 0) {
            return back()->with('error', __('messages.restaurant_settings_not_found'));
        }

        return back()->with('success', __('messages.restaurant_settings_toggled'));
    }

    public function destroy(Request $request)
    {
        $locale = session('locale', 'id');
        app()->setLocale($locale);

        if ($redir = $this->requireLogin()) {
            return $redir;
        }

        $this->assertCanManage();

        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
        ]);

        $id = (int) $validated['id'];

        $deleted = DB::table('restaurant_settings')->where('id', $id)->delete();
        if ($deleted <= 0) {
            return back()->with('error', __('messages.restaurant_settings_not_found'));
        }

        return back()->with('success', __('messages.restaurant_settings_deleted'));
    }
}
