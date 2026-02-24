<?php

namespace App\Providers;

use App\Services\CurrencyRateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layouts.app', function ($view) {
            $rates = null;

            try {
                /** @var CurrencyRateService $service */
                $service = app(CurrencyRateService::class);
                $base = config('services.currency_rates.base_currency', 'UAH');
                $rates = [
                    'USD' => $service->convert(1, 'USD', $base),
                    'EUR' => $service->convert(1, 'EUR', $base),
                    'UAH' => 1.0,
                ];
            } catch (\Throwable $exception) {
                Log::warning('Unable to fetch currency rates for header snippet', [
                    'message' => $exception->getMessage(),
                ]);
            }

            $view->with('headerRates', $rates);
        });
    }
}
