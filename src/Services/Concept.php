<?php

namespace Alariva\LaravelCashflow\Services;

use Alariva\LaravelCashflow\Services\Frequency\FrequencyRule;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class Concept
{
    public function __construct(
        public string $flow,
        public string $name,
        public float $baseAmount,
        public FrequencyRule $frequency,
        public int $quantity,
        public ?string $category = null,
        public ?CarbonInterface $startDate = null,
        public string $currency = 'ARS'
    ) {
        $this->startDate ??= Carbon::now();
    }
}
