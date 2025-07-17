<?php

namespace Alariva\LaravelCashflow\Services\Inflation;

class CompoundMonthlyInflation implements InflationAdjuster
{
    protected float $monthlyRate;

    public function __construct(float $monthlyRate)
    {
        $this->monthlyRate = $monthlyRate;
    }

    public function adjust(float $amount, int $months): float
    {
        $base = (float) (1 + $this->monthlyRate);
        $exponent = (float) $months;

        return $amount * ($base ** $exponent);
    }
}
