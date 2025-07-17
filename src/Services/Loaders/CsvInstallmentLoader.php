<?php

namespace Alariva\LaravelCashflow\Services\Loaders;

use Alariva\LaravelCashflow\Services\CashflowEntry;
use Carbon\Carbon;
use RuntimeException;
use InvalidArgumentException;

class CsvInstallmentLoader
{
    /**
     * @return CashflowEntry[]
     */
    public function load(string $path, Carbon $fromDate): array
    {
        $entries = [];

        if (!file_exists($path)) {
            throw new RuntimeException("File not found: $path");
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new RuntimeException("Could not open file: $path");
        }

        $headers = fgetcsv($handle, 1000, ';');

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            [$flow, $name, $startDate, $installments, $monthlyAmount, $currency, $card] = array_map('trim', $data);

            if (!in_array($flow, ['in', 'out'])) {
                throw new InvalidArgumentException("Invalid flow: $flow");
            }

            $date = Carbon::createFromFormat('Y-m-d', $startDate);
            $amount = (float) $monthlyAmount;

            for ($i = 0; $i < (int) $installments; $i++) {
                $installmentDate = $date->copy()->addMonths($i);

                if ($installmentDate->lt($fromDate)) {
                    continue;
                }

                $entries[] = new CashflowEntry(
                    flow: $flow,
                    conceptName: $card ?: $name,
                    date: $installmentDate,
                    amount: $amount,
                    currency: $currency ?: 'ARS'
                );
            }
        }

        fclose($handle);

        return $entries;
    }
}
