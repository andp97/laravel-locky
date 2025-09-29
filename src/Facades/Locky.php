<?php

namespace Pavons\Locky\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Pavons\Locky\Locky make(string $key)
 */
class Locky extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'locky';
    }
}
