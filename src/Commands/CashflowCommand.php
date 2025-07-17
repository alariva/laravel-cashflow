<?php

namespace Alariva\Cashflow\Commands;

use Illuminate\Console\Command;

class CashflowCommand extends Command
{
    public $signature = 'laravel-cashflow';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
