<?php

declare(strict_types=1);

namespace Windy\LaravelCacheFallback;

use Illuminate\Support\ServiceProvider;

/**
 * Provide cache fallback singletons.
 */
class CacheFallbackServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // We replace the cache manager by our implementation
        $this->app->singleton('cache', static function ($app) {
            return new CacheManagerProxy($app);
        });

        $this->app->singleton('cache.store', static function ($app) {
            return $app['cache']->driver();
        });
    }
}
