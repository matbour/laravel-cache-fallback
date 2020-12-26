<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Illuminate\Support\Facades\Cache;
use Windy\CacheFallback\RepositoryProxy;

class FallbackTest extends CacheFallbackTestCase
{
    public function testInInstalled(): void
    {
        $store = Cache::store();
        $this->assertInstanceOf(RepositoryProxy::class, $store);
    }

    public function testPut(): void
    {
        $this->assertTrue(Cache::put('foo', 'bar'));
    }

    public function testGet(): void
    {
        $this->assertEquals('bar', Cache::get('foo'));
    }
}
