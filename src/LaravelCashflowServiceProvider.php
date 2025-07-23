<?php

namespace Alariva\LaravelCashflow;

use Alariva\LaravelCashflow\Commands\CashflowCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCashflowServiceProvider extends PackageServiceProvider
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

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'cashflow-migrations');
    }
}
