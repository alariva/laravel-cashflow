<?php

namespace Alariva\LaravelCashflow\Services\Frequency;

use Carbon\Carbon;

class QuarterlyRule implements FrequencyRule
{
    public function generateDates(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy()->startOfMonth();

        while ($current <= $end) {
            $dates[] = $current->copy();
            $current->addMonths(3);
        }

        return $dates;
    }
}
