<?php

namespace App\Http\Controllers;

class ComponentController extends Controller
{
    /**
     * Display component library page
     */
    public function index()
    {
        return view('pages.components');
    }
}
