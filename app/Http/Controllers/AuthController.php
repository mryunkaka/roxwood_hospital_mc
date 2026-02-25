<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        return view('pages.login');
    }

    /**
     * Tampilkan halaman register
     */
    public function showRegister()
    {
        return view('pages.register');
    }

    /**
     * Proses login (UI only - langsung redirect ke dashboard)
     */
    public function login(Request $request)
    {
        // Simpan user session sederhana untuk demo
        Session::put('user', [
            'name' => $request->full_name ?? 'Admin User',
            'pin' => $request->pin ?? '1234',
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Proses register (UI only - langsung redirect ke dashboard)
     */
    public function register(Request $request)
    {
        // Simpan user session sederhana untuk demo
        Session::put('user', [
            'name' => $request->full_name ?? 'New User',
            'email' => 'user@roxwood.com',
            'role' => $request->role ?? 'Staff',
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login');
    }
}
