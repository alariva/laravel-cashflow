<?php

namespace Alariva\LaravelCashflow\Services\Support;

use Alariva\LaravelCashflow\Services\CashflowEntry;
use Carbon\Carbon;

class CashflowBreakdownBuilder
{
    /**
     * @param CashflowEntry[] $entries
     */
    public function build(array $entries): array
    {
        $conceptsMap = [];
        $monthCurrencyLabels = [];

        foreach ($entries as $entry) {
            $month = $entry->date->format('M Y');
            $currency = $entry->currency;
            $monthCurrencyLabels["$month|$currency"] = true;

            $key = "{$entry->flow}|{$entry->conceptName}|{$currency}";

            if (!isset($conceptsMap[$key])) {
                $conceptsMap[$key] = [
                    'name'     => $entry->conceptName,
                    'flow'     => $entry->flow,
                    'currency' => $currency,
                    'amounts'  => [],
                ];
            }

            $conceptsMap[$key]['amounts'][$month] = ($conceptsMap[$key]['amounts'][$month] ?? 0) + $entry->amount;
        }

        // Extract distinct months
        $monthSet = [];
        foreach (array_keys($monthCurrencyLabels) as $pair) {
            [$month] = explode('|', $pair);
            $monthSet[$month] = true;
        }

        $months = array_keys($monthSet);
        usort($months, fn($a, $b) => Carbon::createFromFormat('M Y', $a)->timestamp <=> Carbon::createFromFormat('M Y', $b)->timestamp);

        // Fill missing months with zero
        foreach ($conceptsMap as &$concept) {
            foreach ($months as $month) {
                if (!isset($concept['amounts'][$month])) {
                    $concept['amounts'][$month] = 0;
                }
            }
            ksort($concept['amounts']);
        }

        return [
            'concepts' => array_values($conceptsMap),
            'months'   => $months,
        ];
    }
}
