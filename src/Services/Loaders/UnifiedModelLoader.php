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
use Illuminate\Contracts\Auth\Authenticatable;

class UnifiedModelLoader
{
    protected int $userId;

    public function __construct(?Authenticatable $user = null)
    {
        $this->userId = $user?->getAuthIdentifier() ?? auth()->id();
    }

    public function forUser(Authenticatable|int $user): static
    {
        $this->userId = is_int($user) ? $user : $user->getAuthIdentifier();
        return $this;
    }

    /**
     * @return Concept[]
     */
    public function loadConcepts(): array
    {
        return CashflowRecord::forUser($this->userId)
            ->where('type', 'concept')
            ->get()
            ->map(fn ($record) => $this->mapToConcept($record))
            ->all();
    }

    /**
     * @return CashflowEntry[]
     */
    public function loadFixedEntries(): array
    {
        return CashflowRecord::forUser($this->userId)
            ->where('type', 'fixed')
            ->get()
            ->map(fn ($record) => $this->mapToFixedEntry($record))
            ->all();
    }

    /**
     * @param Carbon $fromDate
     * @return CashflowEntry[]
     */
    public function loadInstallmentEntries(Carbon $fromDate): array
    {
        $entries = [];

        $records = CashflowRecord::forUser($this->userId)
            ->where('type', 'installment')
            ->get();

        foreach ($records as $record) {
            $entries = array_merge($entries, $this->mapToInstallments($record, $fromDate));
        }

        return $entries;
    }

    protected function mapToConcept(CashflowRecord $record): Concept
    {
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
    }

    protected function mapToFixedEntry(CashflowRecord $record): CashflowEntry
    {
        $details = $record->details;

        return new CashflowEntry(
            flow: $record->flow,
            conceptName: $record->name,
            date: Carbon::parse($details['date']),
            amount: (float) $details['amount'],
            currency: $record->currency
        );
    }

    protected function mapToInstallments(CashflowRecord $record, Carbon $fromDate): array
    {
        $details = $record->details;
        $amount = (float) $details['amount'];
        $installments = (int) $details['installments'];
        $startDate = Carbon::parse($details['start_date']);

        $entries = [];

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

        return $entries;
    }
}
