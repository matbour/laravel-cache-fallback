<?php

declare(strict_types=1);

namespace Windy\CacheFallback;

use Illuminate\Support\ServiceProvider;

/**
 * Provide cache fallback singletons.
 */
class CacheFallbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // We replace the cache manager by our implementation
        $this->app->extend('cache', function () {
            return new CacheManagerProxy($this->app);
        });

        $this->app->extend('cache.store', function () {
            return $this->app['cache']->driver();
        });
    }
}
