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

            $attempts = 8;
            $delayUs = 50_000; // 50ms base
            $lastError = null;

            for ($i = 0; $i < $attempts; $i++) {
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

