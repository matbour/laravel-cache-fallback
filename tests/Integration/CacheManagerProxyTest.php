<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Illuminate\Contracts\Container\BindingResolutionException;
use Windy\CacheFallback\CacheManagerProxy;
use Windy\CacheFallback\RepositoryProxy;

/**
 * @coversDefaultClass \Windy\CacheFallback\CacheManagerProxy
 */
class CacheManagerProxyTest extends IntegrationTestCase
{
    /**
     * @testdox returns the cache store name.
     *
     * @throws BindingResolutionException
     */
    public function testGetStoreName(): void
    {
        /** @var CacheManagerProxy $manager */
        $manager = $this->app->make('cache');
        /** @var RepositoryProxy $store */
        $store = $this->fallbackStore();
        $this->assertEquals('fallback', $manager->getStoreName($store));
    }
}
