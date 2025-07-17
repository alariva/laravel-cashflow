<?php

namespace Alariva\LaravelCashflow\Services\Frequency;

use Carbon\Carbon;

class SemiannualRule implements FrequencyRule
{
    public function generateDates(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy()->startOfMonth();

        while ($current <= $end) {
            $dates[] = $current->copy();
            $current->addMonths(6);
        }

        return $dates;
    }
}
