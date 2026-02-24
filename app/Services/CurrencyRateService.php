<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CurrencyRateService
{
    public function __construct(
        private ?string $baseUrl = null,
        private ?int $cacheTtl = null,
        private ?string $apiKey = null,
        private ?string $apiKeyLocation = null,
        private ?string $apiKeyName = null,
        private ?string $apiKeyHeaderPrefix = null,
    ) {
        $this->baseUrl = $this->baseUrl ?: config('services.currency_rates.base_url');
        $this->cacheTtl = $this->cacheTtl ?: (int) config('services.currency_rates.cache_seconds');
        $this->apiKey = $this->apiKey ?? config('services.currency_rates.api_key');
        $this->apiKeyLocation = $this->apiKeyLocation ?? config('services.currency_rates.api_key_location');
        $this->apiKeyName = $this->apiKeyName ?? config('services.currency_rates.api_key_name');
        $this->apiKeyHeaderPrefix = $this->apiKeyHeaderPrefix ?? config('services.currency_rates.api_key_header_prefix');
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
            $rates = $this->fetchRates($baseCurrency);

            if ($rates === null) {
                throw new RuntimeException('Unable to fetch currency rates.');
            }

            return $rates;
        });
    }

    private function fetchRates(string $baseCurrency): ?array
    {
        try {
            $query = [
                'base' => strtoupper($baseCurrency),
            ];

            $query = $this->applyApiKeyToQuery($query);
            $request = $this->applyApiKeyToHeaders(Http::timeout(5));

            $response = $request->get($this->baseUrl, $query);

            if ($response->failed()) {
                Log::warning('Currency rate API failed', ['status' => $response->status()]);
                return null;
            }

            $rates = $response->json('rates');

            if (! is_array($rates)) {
                Log::warning('Currency rate API returned unexpected payload');
                return null;
            }

            return array_change_key_case($rates, CASE_UPPER);
        } catch (\Throwable $exception) {
            Log::warning('Currency rate API error', ['message' => $exception->getMessage()]);
            return null;
        }
    }

    /**
     * @param array<string, string> $query
     * @return array<string, string>
     */
    private function applyApiKeyToQuery(array $query): array
    {
        if ($this->apiKey && $this->apiKeyName && $this->apiKeyLocation === 'query') {
            $query[$this->apiKeyName] = $this->apiKey;
        }

        return $query;
    }

    private function applyApiKeyToHeaders(PendingRequest $request): PendingRequest
    {
        if ($this->apiKey && $this->apiKeyName && $this->apiKeyLocation === 'header') {
            $request = $request->withHeaders([
                $this->apiKeyName => $this->formatHeaderValue($this->apiKey),
            ]);
        }

        return $request;
    }

    private function formatHeaderValue(string $value): string
    {
        if ($this->apiKeyHeaderPrefix) {
            return $this->apiKeyHeaderPrefix.$value;
        }

        return $value;
    }

}
