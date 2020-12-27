<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Illuminate\Support\Facades\Cache;
use Windy\CacheFallback\RepositoryProxy;

class CacheFallbackServiceProviderTest extends IntegrationTestCase
{
    /**
     * @testdox The service provider is registered.
     * @covers \Windy\CacheFallback\CacheFallbackServiceProvider::register
     * @covers \Windy\CacheFallback\CacheManagerProxy
     * @covers \Windy\CacheFallback\RepositoryProxy::__construct
     */
    public function testRegister(): void
    {
        $store = Cache::store();
        $this->assertInstanceOf(RepositoryProxy::class, $store);
    }
}
