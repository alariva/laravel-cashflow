<?php

namespace Alariva\LaravelCashflow\Services\Inflation;

interface InflationAdjuster
{
    public function adjust(float $baseAmount, int $monthIndex): float;
}
