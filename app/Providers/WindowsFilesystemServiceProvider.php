<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;

class WindowsFilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // On Windows, the atomic rename() in Filesystem::replace() fails with
        // "Access is denied" when the destination file is locked by another process.
        // Override replace() to use direct file_put_contents on Windows.
        if (PHP_OS_FAMILY === 'Windows') {
            $this->app->extend('files', function (Filesystem $files) {
                return new class($files) extends Filesystem {
                    public function replace($path, $content, $mode = null): void
                    {
                        clearstatcache(true, $path);
                        $path = realpath($path) ?: $path;
                        file_put_contents($path, $content, LOCK_EX);
                    }
                };
            });
        }
    }
}
