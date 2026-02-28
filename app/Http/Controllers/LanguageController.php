<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Get translations as JSON
     */
    public function getTranslations($code)
    {
        // Validate language code
        $availableLangs = ['en', 'id'];

        if (!in_array($code, $availableLangs, true)) {
            $code = 'en';
        }

        try {
            $translations = [];
            $langFile = lang_path("{$code}/messages.php");

            if (is_file($langFile)) {
                $translations = require $langFile;
            }

            if (!is_array($translations)) {
                $translations = [];
            }

            return response()->json($translations);
        } catch (\Throwable $e) {
            report($e);
            return response()->json([]);
        }
    }

    /**
     * Switch language (AJAX/POST)
     */
    public function switch(Request $request, $code)
    {
        // Validate language code
        $availableLangs = ['en', 'id'];

        if (in_array($code, $availableLangs)) {
            Session::put('locale', $code);
            App::setLocale($code);
        }

        // Return JSON response untuk AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'locale' => $code
            ]);
        }

        // Redirect back untuk non-AJAX requests
        return redirect()->back();
    }
}
