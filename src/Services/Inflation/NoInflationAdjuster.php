<?php

namespace Alariva\LaravelCashflow\Services\Inflation;

class NoInflationAdjuster implements InflationAdjuster
{
    public function adjust(float $baseAmount, int $monthIndex): float
    {
        return $baseAmount;
    }
}
