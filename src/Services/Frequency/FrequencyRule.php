<?php

namespace Alariva\LaravelCashflow\Services\Frequency;

interface FrequencyRule
{
    /**
     * @return \Carbon\Carbon[] Lista de fechas de ocurrencia dentro del período dado
     */
    public function generateDates(\Carbon\Carbon $start, \Carbon\Carbon $end): array;
}
