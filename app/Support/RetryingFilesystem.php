<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class RetryingFilesystem extends Filesystem
{
    /**
     * Write the contents of a file, replacing it atomically if it already exists.
     *
     * Windows bisa melempar "Access is denied" pada rename saat file sedang di-scan/locked.
     * Kita tambahkan retry kecil supaya tidak error random saat refresh.
     *
     * @param  string  $path
     * @param  string  $content
     * @param  int|null  $mode
     * @return void
     */
    public function replace($path, $content, $mode = null)
    {
        clearstatcache(true, $path);

        $path = realpath($path) ?: $path;
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);

        $dir = dirname($path);
        if (! is_dir($dir)) {
            $this->ensureDirectoryExists($dir);
        }

        $tempPath = tempnam($dir, basename($path));

        if ($tempPath === false) {
            throw new RuntimeException('Failed to create temporary file for: '.$path);
        }

        try {
            if (! is_null($mode)) {
                @chmod($tempPath, $mode);
            } else {
                @chmod($tempPath, 0777 - umask());
            }

            file_put_contents($tempPath, $content);

            $attempts = 20;
            $delayUs = 40_000; // 40ms base
            $lastError = null;

            for ($i = 0; $i < $attempts; $i++) {
                // Best-effort: if target exists and is replaceable, try removing it first.
                if (is_file($path)) {
                    @unlink($path);
                }

                if (@rename($tempPath, $path)) {
                    return;
                }

                $lastError = error_get_last();
                usleep($delayUs * ($i + 1));
            }

            // Fallback: copy then unlink (best effort)
            if (@copy($tempPath, $path)) {
                @unlink($tempPath);
                return;
            }

            $msg = $lastError['message'] ?? 'Unknown error';
            throw new RuntimeException('Failed to replace file: '.$path.' ('.$msg.')');
        } finally {
            // Cleanup temp if still exists
            if (is_string($tempPath) && file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }
}
