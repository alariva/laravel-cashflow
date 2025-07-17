<?php

namespace Alariva\LaravelCashflow\Services;

use Carbon\Carbon;

class CashflowEntry
{
    public string $flow;
    public string $conceptName;
    public Carbon $date;
    public float $amount;
    public string $currency;

    public function __construct(string $flow, string $conceptName, Carbon $date, float $amount, string $currency = 'ARS')
    {
        $this->flow = $flow;
        $this->conceptName = $conceptName;
        $this->date = $date;
        $this->amount = $amount;
        $this->currency = $currency;
    }
}
