<?php

namespace Alariva\Cashflow;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Alariva\Cashflow\Commands\CashflowCommand;

class CashflowServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cashflow')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_cashflow_table')
            ->hasCommand(CashflowCommand::class);
    }
}
