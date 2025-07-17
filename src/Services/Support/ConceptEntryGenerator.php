<?php

namespace Alariva\LaravelCashflow\Services\Support;

use Alariva\LaravelCashflow\Services\Inflation\InflationAdjuster;
use Alariva\LaravelCashflow\Services\Inflation\NoInflationAdjuster;
use Alariva\LaravelCashflow\Services\CashflowEntry;
use Alariva\LaravelCashflow\Services\Concept;
use Carbon\Carbon;

class ConceptEntryGenerator
{
    /**
     * @var array<string, InflationAdjuster>
     */
    protected array $inflationPerCurrency;

    public function __construct(array $inflationPerCurrency)
    {
        $this->inflationPerCurrency = $inflationPerCurrency;
    }

    /**
     * @param Concept[] $concepts
     * @return CashflowEntry[]
     */
    public function generate(array $concepts, Carbon $start, Carbon $end): array
    {
        $entries = [];

        foreach ($concepts as $concept) {
            $currency = $concept->currency ?? 'ARS';

            $inflation = $this->inflationPerCurrency[$currency] ?? new NoInflationAdjuster();

            $effectiveStart = $concept->startDate ?? $start;
            $fromDate = $effectiveStart->greaterThan($start) ? $effectiveStart : $start;

            $dates = $concept->frequency->generateDates($fromDate, $end);

            foreach ($dates as $date) {
                $monthsSinceStart = $start->diffInMonths($date);
                $adjustedAmount = $inflation->adjust($concept->baseAmount, $monthsSinceStart);
                $totalAmount = $adjustedAmount * $concept->quantity;

                $entries[] = new CashflowEntry(
                    $concept->flow,
                    $concept->name ?? $concept->category ?? 'General',
                    $date,
                    $totalAmount,
                    $currency
                );
            }
        }

        return $entries;
    }
}
