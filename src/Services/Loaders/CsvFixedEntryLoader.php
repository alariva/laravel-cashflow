<?php

namespace Alariva\LaravelCashflow\Services\Loaders;

use Alariva\LaravelCashflow\Services\CashflowEntry;
use Carbon\Carbon;
use InvalidArgumentException;
use RuntimeException;

class CsvFixedEntryLoader
{
    /**
     * @return CashflowEntry[]
     */
    public function load(string $path): array
    {
        $entries = [];

        if (! file_exists($path)) {
            throw new RuntimeException("File not found: $path");
        }

        $handle = fopen($path, 'r');
        if (! $handle) {
            throw new RuntimeException("Could not open file: $path");
        }

        $headers = fgetcsv($handle, 1000, ';');

        while (($data = fgetcsv($handle, 1000, ';')) !== false) {
            [$flow, $name, $amount, $currency, $dateStr, $quantity] = array_map('trim', $data);

            if (! in_array($flow, ['in', 'out'])) {
                throw new InvalidArgumentException("Invalid flow: $flow");
            }

            $date = Carbon::createFromFormat('Y-m-d', $dateStr);

            $entries[] = new CashflowEntry(
                flow: $flow,
                conceptName: $name,
                date: $date,
                amount: (float) $amount * ($quantity ?: 1),
                currency: $currency ?: 'ARS'
            );
        }

        fclose($handle);

        return $entries;
    }
}
