# Laravel Cache Fallback
![GitHub license](https://img.shields.io/github/license/mathieu-bour/laravel-cache-fallback?style=flat-square)
![Packagist Version](https://img.shields.io/packagist/v/mathieu-bour/laravel-cache-fallback?style=flat-square)
![Packagist](https://img.shields.io/packagist/dt/mathieu-bour/laravel-cache-fallback?style=flat-square)
![GitHub issues](https://img.shields.io/github/issues/mathieu-bour/laravel-cache-fallback?style=flat-square)
![GitHub pull requests](https://img.shields.io/github/issues-pr/mathieu-bour/laravel-cache-fallback?style=flat-square)
![Codecov](https://img.shields.io/codecov/c/gh/mathieu-bour/laravel-cache-fallback?style=flat-square)
![Scrutinizer code quality](https://img.shields.io/scrutinizer/quality/g/mathieu-bour/laravel-cache-fallback?style=flat-square)

Allow Laravel cache connections to fallback to more stable drivers.

This package is especially useful when you want your application to be **fault-tolerant**.
For example, when using a Redis instance as cache store, you may want to be able to fallback to file store if the Redis instance is unavailable.

This package follows the [Semantic Versioning specification](https://semver.org).

## Acknowledgements
This package was inspired by [fingo/laravel-cache-fallback](https://github.com/fingo/laravel-cache-fallback), even if it is not maintained anymore.

## Prerequisites
- PHP >= 7.1.3
- Laravel/Lumen 5.8, 6.x, 7.x or 8.x

## Supported cache methods
This package support most of the cache methods (e.g. get, put, etc.).
**The tagged cache is not supported at the moment.**

### Compatibility Matrix

This package was tested against the following matrix:

| Laravel/Lumen | PHP 7.1            | PHP 7.2            | PHP 7.3            | PHP 7.4            | PHP 8.0            |
|---------------|--------------------|--------------------|--------------------|--------------------|--------------------|
| 5.8           | :heavy_check_mark: | :heavy_check_mark: | :heavy_check_mark: | :heavy_check_mark: | :x:                |
| 6.0           | :x:                | :heavy_check_mark: | :heavy_check_mark: | :heavy_check_mark: | :x:                |
| 7.0           | :x:                | :heavy_check_mark: | :heavy_check_mark: | :heavy_check_mark: | :x:                |
| 8.0           | :x:                | :x:                | :heavy_check_mark: | :heavy_check_mark: | :x:                |


## Installation
Simply add `mathieu-bour/laravel-cache-fallback` to your package dependencies.

```bash
composer require mathieu-bour/laravel-cache-fallback
```

This package does not publish any resource and its configuration directly handled in the `config/cache.php` file.

### Laravel
This package uses [Laravel Package Discovery](https://laravel.com/docs/7.x/packages#package-discovery), so you do need to do anything more.
If you have disabled this feature, you can register the service provider in the `config/app.php`.

### Lumen
Register the service provider in the `bootstrap/app.php` file like so:

```php
$app->register(Windy\CacheFallback\CacheFallbackServiceProvider::class);
```

## Usage
Each cache store can now have a `fallback` key in its configuration.
If during execution, the cache driver throws an exception, the fallback driver will be used instead.

```php
<?php

return [
    'default' => env('CACHE_DRIVER', 'file'),
    'stores'  => [
        'file'  => [
            'driver'   => 'file',
            'path'     => storage_path('framework/cache/data'),
        ],
        'redis' => [
            'driver'     => 'redis',
            'connection' => env('CACHE_REDIS_CONNECTION', 'cache'),
            'fallback'   => 'file',
        ],
    ],
];
```

In this example, the `redis` store fallbacks to `file`.

If there is no `fallback` defined, the original exception will be raised as usual.
Note that this package does not provide any way to cycle through stores: if A fallbacks to B and B fallbacks to A, B will throw the exception immediately.
