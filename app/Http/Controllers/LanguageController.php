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

        if (!in_array($code, $availableLangs)) {
            $code = 'en';
        }

        // Load translations
        $translations = [];
        $langFile = base_path("lang/{$code}/messages.php");

        if (file_exists($langFile)) {
            $translations = include $langFile;
        }

        return response()->json($translations);
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
