<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most templating systems load templates from disk. Here you may specify
    | an array of paths that should be checked for your views.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | This option determines where all the compiled Blade templates will be
    | stored. Typically, this is within the storage directory.
    |
    | NOTE (Windows/dev):
    | If the default storage path becomes unreadable due to ACL issues,
    | we automatically fall back to a runtime folder.
    |
    */

    'compiled' => (static function (): string {
        $preferred = (string) env('VIEW_COMPILED_PATH', '');
        $default = storage_path('framework/views');
        $fallback = storage_path('framework/views_runtime');

        $candidates = array_values(array_filter([
            $preferred !== '' ? $preferred : null,
            $default,
            $fallback,
        ]));

        foreach ($candidates as $path) {
            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            if (is_dir($path) && is_readable($path) && is_writable($path)) {
                return $path;
            }
        }

        return $default;
    })(),
];
