<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display patients list page
     */
    public function index()
    {
        return view('pages.patients');
    }
}
