<?php

namespace Alariva\LaravelCashflow\Services\Frequency;

use Carbon\Carbon;

class WeeklyRule implements FrequencyRule
{
    public function generateDates(Carbon $start, Carbon $end): array
    {
        $dates = [];
        $current = $start->copy()->startOfDay();

        while ($current <= $end) {
            $dates[] = $current->copy();
            $current->addWeek();
        }

        return $dates;
    }
}
