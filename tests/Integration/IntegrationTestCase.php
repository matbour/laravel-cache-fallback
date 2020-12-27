<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Container\BindingResolutionException;
use Windy\CacheFallback\CacheFallbackServiceProvider;
use Windy\Hydra\Testing\HydraTestCase;

/**
 * Base test case for the package.
 */
abstract class IntegrationTestCase extends HydraTestCase
{
    protected function setUpLumen(): void
    {
        // We need to set path.storage since the laravel helpers will be used.
        $this->app->instance('path.storage', $this->app->basePath('storage'));
    }

    /**
     * @return mixed[] The package configuration.
     */
    public function setUpConfig(): array
    {
        return [
            'database' => [
                'redis' => [
                    'client' => 'predis',
                    'cache'  => [
                        'host' => '127.0.0.1',
                        'port' => '6666', // invalid port
                    ],
                ],
            ],
            'cache'    => [
                'default' => 'redis',
                'stores'  => [
                    // The fallback cache driver
                    'fallback' => [
                        'driver' => 'array',
                    ],

                    // There is not Redis instance running so the cache will fail
                    'redis'    => [
                        'driver'     => 'redis',
                        'connection' => 'cache',
                        'fallback'   => 'fallback',
                    ],

                    // Failing Redis without fallback
                    'no-fallback'    => [
                        'driver'     => 'redis',
                        'connection' => 'cache',
                    ],

                    // Loop Redis A -> Redis B -> Redis A
                    'redisA'   => [
                        'driver'     => 'redis',
                        'connection' => 'cache',
                        'fallback'   => 'redisB',
                    ],
                    'redisB'   => [
                        'driver'     => 'redis',
                        'connection' => 'cache',
                        'fallback'   => 'redisA',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string[] The package providers.
     */
    public function setUpProviders(): array
    {
        return [
            CacheFallbackServiceProvider::class,
        ];
    }

    /**
     * Get the fallback cache store, useful when testing against the default cache.
     *
     * @throws BindingResolutionException
     */
    protected function fallbackStore(): Repository
    {
        return $this->app->make('cache')->store('fallback');
    }

    /**
     * Get the failing cache store.
     *
     * @throws BindingResolutionException
     */
    protected function failingStore(): Repository
    {
        return $this->app->make('cache')->store('redis');
    }
}
