<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Container\BindingResolutionException;
use Predis\Connection\ConnectionException;
use Psr\SimpleCache\InvalidArgumentException;
use function array_keys;

/**
 * @coversDefaultClass \Windy\CacheFallback\RepositoryProxy
 */
class RepositoryProxyTest extends IntegrationTestCase
{
    /**
     * @testdox RedisA and RedisB do not loop over themselves.
     * @covers ::next
     *
     * @throws BindingResolutionException
     */
    public function testNoLoop(): void
    {
        /** @var CacheManager $manager */
        $manager = $this->app->make('cache');
        $redisA  = $manager->store('redisA');

        $this->expectException(ConnectionException::class);
        $redisA->put('foo', 'bar', 10);
    }

    /**
     * @testdox throws normally when there is no fallback store.
     *
     * @throws BindingResolutionException
     */
    public function testNoFallback(): void
    {
        /** @var CacheManager $manager */
        $manager = $this->app->make('cache');
        $redisA  = $manager->store('no-fallback');

        $this->expectException(ConnectionException::class);
        $redisA->put('foo', 'bar', 10);
    }

    /**
     * @testdox get fallbacks to array store.
     * @covers ::get
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testGet(): void
    {
        // We use the fallback array cache store
        $this->fallbackStore()->set('foo', 'bar', 10);

        $this->assertEquals('bar', $this->failingStore()->get('foo'));
    }

    /**
     * @testdox many fallbacks to array store.
     * @covers ::many
     *
     * @throws BindingResolutionException
     */
    public function testMany(): void
    {
        $data = ['a' => 'foo', 'b' => 'bar',];

        $this->fallbackStore()->putMany($data, 10);
        $this->assertEquals($data, $this->failingStore()->many(array_keys($data)));
    }

    /**
     * @testdox put fallbacks to array store.
     * @covers ::put
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testPut(): void
    {
        $this->assertTrue($this->failingStore()->put('foo', 'bar', 10));
        $this->assertEquals('bar', $this->fallbackStore()->get('foo'));
    }

    /**
     * @testdox putMany fallbacks to array store.
     * @covers ::putMany
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testPutMany(): void
    {
        $this->assertTrue($this->failingStore()->putMany([
            'a' => 'foo',
            'b' => 'bar',
        ], 10));
        $this->assertEquals('foo', $this->fallbackStore()->get('a'));
        $this->assertEquals('bar', $this->fallbackStore()->get('b'));
    }

    /**
     * @testdox add fallbacks to array store.
     * @covers ::add
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testAdd(): void
    {
        $this->assertTrue($this->failingStore()->add('foo', 'bar', 10));
        $this->assertEquals('bar', $this->fallbackStore()->get('foo'));
    }

    /**
     * @testdox increment fallbacks to array store.
     * @covers ::increment
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testIncrement(): void
    {
        $this->assertTrue($this->fallbackStore()->set('counter', 10, 10));
        $this->assertEquals(11, $this->failingStore()->increment('counter'));
    }

    /**
     * @testdox decrement fallbacks to array store.
     * @covers ::decrement
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testDecrement(): void
    {
        $this->assertTrue($this->fallbackStore()->set('counter', 10, 10));
        $this->assertEquals(9, $this->failingStore()->decrement('counter'));
    }

    /**
     * @testdox forever fallbacks to array store.
     * @covers ::forever
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testForever(): void
    {
        $this->assertTrue($this->failingStore()->forever('foo', 'bar'));
        $this->assertEquals('bar', $this->fallbackStore()->get('foo'));
    }

    /**
     * @testdox forget fallbacks to array store.
     * @covers ::forget
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testForget(): void
    {
        $this->fallbackStore()->set('foo', 'bar', 10);

        $this->assertEquals('bar', $this->failingStore()->get('foo'));
        $this->assertTrue($this->failingStore()->forget('foo'));
        $this->assertNull($this->failingStore()->get('foo'));
    }

    /**
     * @testdox clear fallbacks to array store.
     * @covers ::clear
     *
     * @throws BindingResolutionException
     * @throws InvalidArgumentException
     */
    public function testClear(): void
    {
        $this->fallbackStore()->set('foo', 'bar', 10);

        $this->assertEquals('bar', $this->failingStore()->get('foo'));
        $this->assertTrue($this->failingStore()->clear());
        $this->assertNull($this->failingStore()->get('foo'));
    }
}
