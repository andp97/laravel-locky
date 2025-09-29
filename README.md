# A simple Laravel package for distributed lock

[![Latest Version on Packagist](https://img.shields.io/packagist/v/andp97/laravel-locky.svg?style=flat-square)](https://packagist.org/packages/andp97/laravel-locky)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/andp97/laravel-locky/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/andp97/laravel-locky/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/andp97/laravel-locky/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/andp97/laravel-locky/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/andp97/laravel-locky.svg?style=flat-square)](https://packagist.org/packages/andp97/laravel-locky)

A fluent, Laravel-y Builder around Redis atomic locks with retry + backoff (with jitter). It’s easy to read, chainable, and testable.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-locky.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-locky)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require andp97/laravel-locky
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-locky-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-locky-config"
```

This is the contents of the published config file:

```php
return [
];
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-locky-views"
```

## Usage

### Basic

```php
use Pavons\Locky\Facades\Locky;

$result = Locky::make("widgets:{$id}")
    ->ttl(10)
    ->run(function () use ($id) {
        processWidget($id);
        return true;
    });
```

#### With retries, exponential backoff, and **full jitter**

```php
use Pavons\Locky\Facades\Locky;

Locky::make("widgets:{$orderId}")
    ->ttl(12)
    ->attempts(7)
    ->baseDelayMs(75)     // 75, 150, 300, 600, 1200, 2000…
    ->multiplier(2.0)
    ->maxDelayMs(2000)
    ->jitter('full')      // none|equal|full
    ->onRetry(function ($key, $attempt, $sleepMs) {
        logger()->notice("Retrying lock", compact('key', 'attempt', 'sleepMs'));
    })
    ->onFail(function ($key, $attempts) {
        // Optional graceful fallback
        report(new \RuntimeException("Lock failed for {$key} after {$attempts} attempts"));
        return null; // Returning here prevents throwing; omit to throw
    })
    ->run(function () use ($orderId) {
        settleOrder($orderId);
    });
```

#### In a queued job (recommended)

```php
public function handle(): void
{
    Locky::make("job:import:{$this->batchId}")
        ->ttl(15)
        ->attempts(8)
        ->jitter('equal')
        ->run(function () {
            $this->performImport(); // exclusive section
        });
}
```

### Notes & best practices

- **Use Redis** (`CACHE_DRIVER=redis`) so `Cache::lock()` is truly distributed.
    
- **Pick TTL > worst-case critical section**, but keep it tight; split long work to keep the locked part short.
    
- **Jitter**:
    
    - `full`: best at smoothing thundering herds (default).
        
    - `equal`: narrows variance while avoiding sync.
        
    - `none`: only if you _really_ want deterministic waits.
        
- Hooks (`onRetry`, `onFail`) make it easy to add logs/metrics without cluttering business code.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Andrea Pavone](https://github.com/andp97)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
