<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Windy\CacheFallback\CacheFallbackServiceProvider;
use Windy\Hydra\Testing\HydraTestCase;

class CacheFallbackTestCase extends HydraTestCase
{
    /**
     * @return mixed[] The package configuration.
     */
    public function setUpConfig(): array
    {
        return [
            'cache' => [
                'default' => 'redis',
                'stores'  => [
                    'file'    => [
                        'driver'   => 'file',
                        'path'     => $this->app->basePath('storage/framework/cache/data'),
                    ],
                    // There is not Redis instance running so the cache will fail
                    'redis'   => [
                        'driver'     => 'redis',
                        'connection' => 'default',
                        'fallback'   => 'file',
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
}
