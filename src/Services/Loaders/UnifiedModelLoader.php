<?php

namespace Alariva\LaravelCashflow\Services\Loaders;

use Alariva\LaravelCashflow\Models\CashflowRecord;
use Alariva\LaravelCashflow\Services\CashflowEntry;
use Alariva\LaravelCashflow\Services\Concept;
use Alariva\LaravelCashflow\Services\Frequency\{
    BimonthlyRule,
    DailyRule,
    MonthlyRule,
    QuarterlyRule,
    SemiannualRule,
    WeeklyRule,
    YearlyRule
};
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UnifiedModelLoader
{
    /**
     * @return Concept[]
     */
    public function loadConcepts(): array
    {
        return CashflowRecord::where('type', 'concept')->get()->map(function ($record) {
            $details = $record->details;

            $rule = match (strtolower($details['frequency'] ?? '')) {
                'daily' => new DailyRule(),
                'weekly' => new WeeklyRule(),
                'monthly' => new MonthlyRule(),
                'bimonthly' => new BimonthlyRule(),
                'quarterly' => new QuarterlyRule(),
                'semiannual' => new SemiannualRule(),
                'yearly' => new YearlyRule(),
                default => throw new \InvalidArgumentException("Invalid frequency: {$details['frequency']}"),
            };

            return new Concept(
                flow: $record->flow,
                name: $record->name,
                baseAmount: (float) $details['base_amount'],
                frequency: $rule,
                quantity: (int) ($details['quantity'] ?? 1),
                category: $details['category'] ?? null,
                startDate: isset($details['start_date']) ? Carbon::parse($details['start_date']) : now(),
                currency: $record->currency
            );
        })->all();
    }

    /**
     * @return CashflowEntry[]
     */
    public function loadFixedEntries(): array
    {
        return CashflowRecord::where('type', 'fixed')->get()->map(function ($record) {
            $details = $record->details;

            return new CashflowEntry(
                flow: $record->flow,
                conceptName: $record->name,
                date: Carbon::parse($details['date']),
                amount: (float) $details['amount'],
                currency: $record->currency
            );
        })->all();
    }

    /**
     * @param Carbon $fromDate
     * @return CashflowEntry[]
     */
    public function loadInstallmentEntries(Carbon $fromDate): array
    {
        $entries = [];

        $records = CashflowRecord::where('type', 'installment')->get();

        foreach ($records as $record) {
            $details = $record->details;

            $amount = (float) $details['amount'];
            $installments = (int) $details['installments'];
            $startDate = Carbon::parse($details['start_date']);

            for ($i = 0; $i < $installments; $i++) {
                $installmentDate = $startDate->copy()->addMonths($i);

                if ($installmentDate->lt($fromDate)) {
                    continue;
                }

                $entries[] = new CashflowEntry(
                    flow: $record->flow,
                    conceptName: $details['card'] ?? $record->name,
                    date: $installmentDate,
                    amount: $amount,
                    currency: $record->currency
                );
            }
        }

        return $entries;
    }
}
