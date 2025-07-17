<?php

namespace Alariva\LaravelCashflow\Services;

use Alariva\LaravelCashflow\Services\Inflation\CompoundMonthlyInflation;
use Alariva\LaravelCashflow\Services\Inflation\InflationAdjuster;
use Alariva\LaravelCashflow\Services\Inflation\NoInflationAdjuster;
use Alariva\LaravelCashflow\Services\Support\CashflowAggregator;
use Alariva\LaravelCashflow\Services\Support\CashflowBreakdownBuilder;
use Alariva\LaravelCashflow\Services\Support\ConceptEntryGenerator;
use Carbon\Carbon;

class CashflowGenerator
{
    protected InflationAdjuster $inflation;
    protected array $concepts = [];
    protected array $installmentEntries = [];
    protected array $fixedEntries = [];

    protected array $inflationPerCurrency = [];

    public function __construct(array $inflationPerCurrency = [])
    {
        // fallback to ARS if not provided
        $this->inflationPerCurrency = $inflationPerCurrency + [
            'ARS' => new CompoundMonthlyInflation(0.0252),
            'USD' => new NoInflationAdjuster(),
            'EUR' => new CompoundMonthlyInflation(0.005),
        ];
    }


    public function setConcepts(array $concepts): void
    {
        $this->concepts = $concepts;
    }

    public function setFixedEntries(array $entries): void
    {
        $this->fixedEntries = $entries;
    }

    public function setInstallmentEntries(array $entries): void
    {
        $this->installmentEntries = $entries;
    }

    public function generate(Carbon $start, Carbon $end): array
    {
        // Generate entries from concept rules
        $conceptGenerator = new ConceptEntryGenerator($this->inflationPerCurrency);
        $conceptEntries = $conceptGenerator->generate($this->concepts, $start, $end);

        // Combine all entries
        $allEntries = array_merge($conceptEntries, $this->fixedEntries, $this->installmentEntries);

        // Aggregate monthly totals and balances
        $aggregator = new CashflowAggregator();
        $summary = $aggregator->summarize($allEntries);

        // Prepare frontend-friendly breakdown
        $breakdown = (new CashflowBreakdownBuilder())->build($allEntries);

        return array_merge($breakdown, [
            'totals' => $summary
        ]);
    }
}
