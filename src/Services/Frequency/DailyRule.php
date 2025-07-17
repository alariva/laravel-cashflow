<?php

namespace Alariva\LaravelCashflow\Services\Frequency;

use Carbon\Carbon;

class DailyRule implements FrequencyRule
{
    public function generateDates(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy()->startOfDay();

        while ($current <= $end) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        return $dates;
    }
}
