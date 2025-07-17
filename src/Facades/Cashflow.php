<?php

namespace Alariva\LaravelCashflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Alariva\LaravelCashflow\Cashflow
 */
class Cashflow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Alariva\LaravelCashflow\Cashflow::class;
    }
}
