<?php

namespace Pavons\Locky;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LockyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-locky')
            ->hasConfigFile();
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind('locky', function () {
            return new Locky;
        });
    }
}
