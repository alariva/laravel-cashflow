<?php

namespace Alariva\Cashflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Alariva\Cashflow\Cashflow
 */
class Cashflow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Alariva\Cashflow\Cashflow::class;
    }
}
