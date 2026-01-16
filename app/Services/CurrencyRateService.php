<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CurrencyRateService
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?int $cacheTtl = null,
    ) {
        $this->baseUrl = $this->baseUrl ?: config('services.currency_rates.base_url');
        $this->cacheTtl = $this->cacheTtl ?: (int) config('services.currency_rates.cache_seconds');
    }

    public function convert(float $amount, string $fromCurrency, string $toCurrency): float
    {
        $fromCurrency = strtoupper($fromCurrency);
        $toCurrency = strtoupper($toCurrency);

        if ($fromCurrency === $toCurrency || $amount == 0.0) {
            return round($amount, 2);
        }

        $rates = $this->getRatesForBase($fromCurrency);
        $rate = $rates[$toCurrency] ?? null;

        if ($rate === null) {
            throw new RuntimeException("Missing currency rate for {$fromCurrency} -> {$toCurrency}");
        }

        return round($amount * $rate, 2);
    }

    /**
     * @return array<string, float>
     */
    private function getRatesForBase(string $baseCurrency): array
    {
        $cacheKey = sprintf('currency_rates_%s', strtoupper($baseCurrency));

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($baseCurrency) {
            $response = Http::timeout(5)->get($this->baseUrl, [
                'base' => strtoupper($baseCurrency),
            ]);

            if ($response->failed()) {
                throw new RuntimeException('Unable to fetch currency rates.');
            }

            $rates = $response->json('rates');

            if (!is_array($rates)) {
                throw new RuntimeException('Unexpected currency API response.');
            }

            return array_change_key_case($rates, CASE_UPPER);
        });
    }
}
