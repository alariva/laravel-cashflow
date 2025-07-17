<?php

namespace Alariva\LaravelCashflow\Services\Support;

use Alariva\LaravelCashflow\Services\CashflowEntry;
use Carbon\Carbon;

class CashflowAggregator
{
    /**
     * @param CashflowEntry[] $entries
     */
    public function summarize(array $entries): array
    {
        $totalsByMonth = [];

        foreach ($entries as $entry) {
            $month = $entry->date->format('M Y');
            $currency = $entry->currency;

            if (!isset($totalsByMonth[$month][$currency])) {
                $totalsByMonth[$month][$currency] = ['in' => 0, 'out' => 0];
            }

            $totalsByMonth[$month][$currency][$entry->flow] += $entry->amount;
        }

        // Sort months chronologically
        uksort($totalsByMonth, function ($a, $b) {
            return Carbon::createFromFormat('M Y', $a)->timestamp <=> Carbon::createFromFormat('M Y', $b)->timestamp;
        });

        // Calculate balance and accumulated values
        $balance = [];
        $accumulated = [];
        $runningTotals = [];

        foreach ($totalsByMonth as $month => $currencyData) {
            foreach ($currencyData as $currency => $totals) {
                $in = $totals['in'] ?? 0;
                $out = $totals['out'] ?? 0;
                $diff = $in - $out;

                $runningTotals[$currency] = ($runningTotals[$currency] ?? 0) + $diff;

                $balance[$month][$currency] = $diff;
                $accumulated[$month][$currency] = $runningTotals[$currency];
            }
        }

        return [
            'by_month'    => $totalsByMonth,
            'balance'     => $balance,
            'accumulated' => $accumulated,
        ];
    }
}
