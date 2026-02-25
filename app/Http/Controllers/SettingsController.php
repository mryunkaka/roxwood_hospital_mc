<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        return view('pages.settings');
    }
}
