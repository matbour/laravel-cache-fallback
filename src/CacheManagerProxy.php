<?php

declare(strict_types=1);

namespace Windy\CacheFallback;

use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use function array_search;

/**
 * From {@see \Illuminate\Contracts\Cache\Repository} and {@see \Psr\SimpleCache\CacheInterface}:
 *
 * @method mixed pull($key, $default = null)
 * @method bool put($key, $value, $ttl = null)
 * @method bool add($key, $value, $ttl = null)
 * @method int|bool increment($key, $value = 1)
 * @method int|bool decrement($key, $value = 1)
 * @method bool forever($key, $value)
 * @method mixed remember($key, $ttl, Closure $callback)
 * @method mixed sear($key, Closure $callback)
 * @method mixed rememberForever($key, Closure $callback)
 * @method bool forget($key)
 * @method Store getStore()
 * ---
 * @method mixed get($key, $default = null)
 * @method bool set($key, $value, $ttl = null)
 * @method bool delete($key)
 * @method bool clear()
 * @method iterable getMultiple($keys, $default = null)
 * @method bool setMultiple($values, $ttl = null)
 * @method bool deleteMultiple($keys)
 * @method bool has($key)
 */
class CacheManagerProxy extends CacheManager
{
    /**
     * Get the name of a cache store.
     *
     * @param RepositoryProxy $store The cache store.
     *
     * @return string|null The cache store if it exists in the {@see CacheManager::$stores}, null otherwise.
     */
    public function getStoreName(RepositoryProxy $store): ?string
    {
        return array_search($store, $this->stores, true) ?: null;
    }

    /**
     * Create a new cache repository with the given implementation using our {@see RepositoryProxy}.
     *
     * @param Store $store The Laravel cache store instance.
     *
     * @return RepositoryProxy The repository proxy.
     */
    public function repository(Store $store): RepositoryProxy
    {
        $repository = new RepositoryProxy($store, $this);

        if ($this->app->bound(DispatcherContract::class)) {
            $repository->setEventDispatcher($this->app[DispatcherContract::class]);
        }

        return $repository;
    }
}
