<?php

namespace App\Services;

use App\Enums\CurrencyCode;
use App\Models\FinanceSnapshot;
use Illuminate\Support\Collection;

class FinanceSnapshotAggregator
{
    public function __construct(private readonly CurrencyRateService $currencyRateService)
    {
    }

    /**
     * @param Collection<int, FinanceSnapshot> $snapshots
     * @return Collection<int, array<string, mixed>>
     */
    public function summarize(Collection $snapshots): Collection
    {
        $baseCurrency = config('services.currency_rates.base_currency', CurrencyCode::UAH->value);

        return $snapshots->map(function (FinanceSnapshot $snapshot) use ($baseCurrency) {
            $totals = $this->calculateTotals($snapshot, $baseCurrency);

            return array_merge($totals, [
                'snapshot' => $snapshot,
                'date' => $snapshot->snapshot_date,
                'note' => $snapshot->note,
            ]);
        });
    }

    public function annotateDifferences(Collection $summaries): Collection
    {
        $ordered = $summaries->sortBy('date')->values();
        $previousTotal = null;

        foreach ($ordered as $index => $summary) {
            $difference = $previousTotal === null
                ? 0
                : round($summary['total_uah'] - $previousTotal, 2);

            $summary['difference'] = $difference;
            $ordered[$index] = $summary;
            $previousTotal = $summary['total_uah'];
        }

        $lookup = $ordered->keyBy(fn ($summary) => $summary['snapshot']->id);

        return $summaries->map(function ($summary) use ($lookup) {
            $annotated = $lookup[$summary['snapshot']->id];
            $summary['difference'] = $annotated['difference'];
            return $summary;
        });
    }

    /**
     * @param Collection<int, array<string, mixed>> $summaries
     * @return array<string, array<string, float>>
     */
    public function spendingIncomeByMonth(Collection $summaries): array
    {
        $ordered = $summaries->sortBy('date')->values();
        $previousTotal = null;
        $result = [];

        foreach ($ordered as $summary) {
            if ($previousTotal === null) {
                $previousTotal = $summary['total_uah'];
                continue;
            }

            $diff = round($summary['total_uah'] - $previousTotal, 2);
            $monthKey = $summary['date']->format('Y-m');

            if (! isset($result[$monthKey])) {
                $result[$monthKey]['income'] = 0.0;
                $result[$monthKey]['spending'] = 0.0;
            }

            if ($diff >= 0) {
                $result[$monthKey]['income'] += $diff;
            } else {
                $result[$monthKey]['spending'] += abs($diff);
            }

            $previousTotal = $summary['total_uah'];
        }

        return $result;
    }

    private function calculateTotals(FinanceSnapshot $snapshot, string $baseCurrency): array
    {
        $totalBase = 0.0;
        $activeBase = 0.0;

        foreach ($snapshot->details as $detail) {
            $converted = $this->currencyRateService->convert(
                (float) $detail->amount,
                $detail->currency_code,
                $baseCurrency,
            );

            $totalBase += $converted;

            if ($detail->is_active) {
                $activeBase += $converted;
            }
        }

        $totalUsd = $this->currencyRateService->convert($totalBase, $baseCurrency, CurrencyCode::USD->value);
        $activeUsd = $this->currencyRateService->convert($activeBase, $baseCurrency, CurrencyCode::USD->value);

        return [
            'total_uah' => round($totalBase, 2),
            'active_total_uah' => round($activeBase, 2),
            'total_usd' => $totalUsd,
            'active_total_usd' => $activeUsd,
        ];
    }
}
