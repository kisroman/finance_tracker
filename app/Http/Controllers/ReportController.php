<?php

namespace App\Http\Controllers;

use App\Models\FinanceSnapshot;
use App\Services\FinanceSnapshotAggregator;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly FinanceSnapshotAggregator $aggregator)
    {
    }

    public function spendingIncome(): View
    {
        $summaries = $this->summaries();

        return view('reports.spending-income', [
            'report' => $this->aggregator->spendingIncomeByMonth($summaries),
        ]);
    }

    public function diagrams(): View
    {
        $summaries = $this->summaries();

        return view('reports.diagrams', [
            'report' => $this->aggregator->spendingIncomeByMonth($summaries),
            'summaries' => $summaries->sortBy('date')->values(),
        ]);
    }

    private function summaries(): Collection
    {
        $snapshots = FinanceSnapshot::with('details')->orderBy('snapshot_date')->get();
        $summaries = $this->aggregator->summarize($snapshots);

        return $this->aggregator->annotateDifferences($summaries);
    }
}
