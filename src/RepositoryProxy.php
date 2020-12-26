<?php

declare(strict_types=1);

namespace Windy\CacheFallback;

use DateInterval;
use DateTimeInterface;
use Illuminate\Cache\Repository as CacheRepository;
use Illuminate\Cache\TaggedCache;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Cache\Store;
use Throwable;
use function func_get_args;
use function tap;

/**
 * Proxy the repository calls and forward then to the underlying cache store.
 *
 * @method bool flush()
 * @method string getPrefix()
 */
class RepositoryProxy extends CacheRepository
{
    private $thrown = false;
    private $manager;

    public function __construct(Store $store, CacheManagerProxy $manager)
    {
        parent::__construct($store);

        $this->manager = $manager;
    }

    /**
     * @param string    $method    The cache method to call.
     * @param mixed[]   $arguments The arguments to pass.
     * @param Throwable $exception The original exception.
     *
     * @return mixed
     *
     * @throws Throwable
     */
    public function next(string $method, array $arguments, Throwable $exception)
    {
        if ($this->thrown) {
            // Prevent infinite loops A fallbacks to B and B fallbacks to A
            throw $exception;
        }

        $this->thrown = true;

        /** @var ConfigRepository $config */
        $config = Container::getInstance()->get('config');

        $name = $this->manager->getStoreName($this);

        if (!$name || !$config->has("cache.stores.{$name}.fallback")) {
            throw $exception;
        }

        $fallback = $config->get("cache.stores.{$name}.fallback");

        return tap($this->manager->store($fallback)->$method(...$arguments), function (): void {
            $this->thrown = false;
        });
    }

    /**
     * @param string     $key     The cache key.
     * @param mixed|null $default The cache default value, if the key does not exist.
     *
     * @return mixed The cached value.
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function get($key, $default = null)
    {
        try {
            return parent::get($key, $default);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $default], $exception);
        }
    }

    /**
     * @param string[] $keys The cache keys.
     *
     * @return mixed[] The cached values.
     *
     * @throws Throwable The bubbled exception.
     */
    public function many(array $keys): array
    {
        try {
            return parent::many($keys);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$keys], $exception);
        }
    }

    /**
     * @param string                                  $key   The cache key.
     * @param mixed                                   $value The cached value.
     * @param DateTimeInterface|DateInterval|int|null $ttl   The cache duration, if any.
     *
     * @return bool If the cache insertion succeeded.
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function put($key, $value, $ttl = null): bool
    {
        try {
            return parent::put($key, $value, $ttl);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $value, $ttl], $exception);
        }
    }

    /**
     * @param mixed[]                                 $values The keys => values cache to insert.
     * @param DateTimeInterface|DateInterval|int|null $ttl    The cache duration, if any.
     *
     * @return bool If the cache insertion succeeded.
     *
     * @throws Throwable The bubbled exception.
     */
    public function putMany(array $values, $ttl = null): bool
    {
        try {
            return parent::putMany($values, $ttl);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$values, $ttl], $exception);
        }
    }

    /**
     * @param string                                  $key   The cache key.
     * @param mixed                                   $value The cached value.
     * @param DateTimeInterface|DateInterval|int|null $ttl   The cache duration, if any.
     *
     * @return bool If the cache insertion succeeded.
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function add($key, $value, $ttl = null): bool
    {
        try {
            return parent::add($key, $value, $ttl);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $value, $ttl], $exception);
        }
    }

    /**
     * @param string $key   The cache key.
     * @param int    $value The increment value (defaults to 1).
     *
     * @return bool|int
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function increment($key, $value = 1)
    {
        try {
            return parent::increment($key, $value);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $value], $exception);
        }
    }

    /**
     * @param string $key   The cache key.
     * @param int    $value The decrement value (defaults to 1).
     *
     * @return bool|int
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function decrement($key, $value = 1)
    {
        try {
            return parent::decrement($key, $value);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $value], $exception);
        }
    }

    /**
     * @param string $key   The cache key.
     * @param mixed  $value The cached value.
     *
     * @return bool If the cache insertion succeeded.
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function forever($key, $value): bool
    {
        try {
            return parent::forever($key, $value);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key, $value], $exception);
        }
    }

    /**
     * @param string $key The cache key to forget.
     *
     * @return bool If the cache deletion succeeded.
     *
     * @throws Throwable The bubbled exception.
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function forget($key): bool
    {
        try {
            return parent::forget($key);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$key], $exception);
        }
    }

    /**
     * @return bool If the cache deletion succeeded.
     *
     * @throws Throwable The bubbled exception.
     */
    public function clear(): bool
    {
        try {
            return parent::clear();
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [], $exception);
        }
    }

    /**
     * @param string|string[] $names The tags names.
     *
     * @return TaggedCache The tagged cache instance.
     *
     * @throws Throwable The bubbled exception.
     */
    public function tags($names): TaggedCache
    {
        try {
            return parent::tags($names);
        } catch (Throwable $exception) {
            return $this->next(__FUNCTION__, [$names], $exception);
        }
    }

    /**
     * Forward a the calls to the underlying cache store instance.
     *
     * @param string  $method     The method name.
     * @param mixed[] $parameters The method arguments.
     *
     * @return mixed
     *
     * @throws Throwable The bubbled exception.
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function __call($method, $parameters)
    {
        try {
            return parent::__call($method, $parameters);
        } catch (Throwable $exception) {
            return $this->next($method, $parameters, $exception);
        }
    }
}
