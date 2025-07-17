<?php

namespace Alariva\LaravelCashflow\Services\Loaders;

use Alariva\LaravelCashflow\Services\Concept;
use Alariva\LaravelCashflow\Services\Frequency\BimonthlyRule;
use Alariva\LaravelCashflow\Services\Frequency\DailyRule;
use Alariva\LaravelCashflow\Services\Frequency\MonthlyRule;
use Alariva\LaravelCashflow\Services\Frequency\QuarterlyRule;
use Alariva\LaravelCashflow\Services\Frequency\SemiannualRule;
use Alariva\LaravelCashflow\Services\Frequency\WeeklyRule;
use Alariva\LaravelCashflow\Services\Frequency\YearlyRule;
use InvalidArgumentException;

class CsvConceptLoader
{
    public function load(string $path): array
    {
        $concepts = [];

        if (!file_exists($path)) {
            throw new \RuntimeException("File not found: $path");
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new \RuntimeException("Could not open file: $path");
        }

        $headers = fgetcsv($handle, 1000, ';'); // skip header

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            [$flow, $name, $amount, $currency, $frequency, $quantity, $category, $startDate] = array_map('trim', $data);

            $rule = match (strtolower($frequency)) {
                'yearly'      => new YearlyRule(),
                'semiannual'  => new SemiannualRule(),
                'quarterly'   => new QuarterlyRule(),
                'bimonthly'   => new BimonthlyRule(),
                'monthly'     => new MonthlyRule(),
                'weekly'      => new WeeklyRule(),
                'daily'       => new DailyRule(),
                default       => throw new InvalidArgumentException("Invalid frequency: $frequency"),
            };

            $concepts[] = new Concept(
                flow: $flow,
                name: $name,
                baseAmount: (float) $amount,
                frequency: $rule,
                quantity: (int) $quantity,
                category: $category ?: null,
                startDate: $startDate ? \Carbon\Carbon::parse($startDate) : now(),
                currency: $currency ?: 'ARS'
            );
        }

        fclose($handle);

        return $concepts;
    }
}
