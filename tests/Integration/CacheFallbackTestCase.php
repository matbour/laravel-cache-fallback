<?php

declare(strict_types=1);

namespace Windy\CacheFallback\Tests\Integration;

use Windy\CacheFallback\CacheFallbackServiceProvider;
use Windy\Hydra\Testing\HydraTestCase;
use function storage_path;

class CacheFallbackTestCase extends HydraTestCase
{
    /**
     * @return array[] The package configuration.
     */
    public function setUpConfig(): array
    {
        return [
            'cache' => [
                'default' => 'redis',
                'stores'  => [
                    'file'    => [
                        'driver'   => 'file',
                        'path'     => storage_path('framework/cache/data'),
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
