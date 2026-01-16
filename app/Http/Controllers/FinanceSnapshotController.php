<?php

namespace App\Http\Controllers;

use App\Enums\CurrencyCode;
use App\Http\Requests\StoreFinanceSnapshotRequest;
use App\Models\FinanceDetail;
use App\Models\FinanceSnapshot;
use App\Models\Stock;
use App\Services\FinanceSnapshotAggregator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinanceSnapshotController extends Controller
{
    public function __construct(private readonly FinanceSnapshotAggregator $aggregator)
    {
    }

    public function index(): View
    {
        $snapshots = FinanceSnapshot::with('details')->orderBy('snapshot_date')->get();
        $summaries = $this->summaries($snapshots)
            ->sortByDesc('date')
            ->values();

        return view('finance.index', [
            'summaries' => $summaries,
        ]);
    }

    public function store(StoreFinanceSnapshotRequest $request): RedirectResponse
    {
        $snapshot = null;

        DB::transaction(function () use ($request, &$snapshot) {
            $snapshot = FinanceSnapshot::create($request->validated());

            $lastSnapshot = FinanceSnapshot::with('details')
                ->where('id', '<>', $snapshot->id)
                ->orderByDesc('snapshot_date')
                ->first();

            if (! $lastSnapshot) {
                return;
            }

            $lastSnapshot->details->each(function (FinanceDetail $detail) use ($snapshot) {
                $snapshot->details()->create([
                    'stock_id' => $detail->stock_id,
                    'source' => $detail->source,
                    'amount' => $detail->amount,
                    'currency_code' => $detail->currency_code,
                    'is_active' => $detail->is_active,
                    'comment' => $detail->comment,
                    'position' => $detail->position,
                ]);
            });
        });

        return redirect()->route('snapshots.show', $snapshot)
            ->with('status', 'Finance snapshot created.');
    }

    public function show(FinanceSnapshot $snapshot): View
    {
        $snapshot->load(['details.stock']);
        $summary = $this->aggregator->summarize(collect([$snapshot]))->first();
        $stocks = Stock::active()->orderBy('name')->get();

        return view('finance.show', [
            'snapshot' => $snapshot,
            'summary' => $summary,
            'stocks' => $stocks,
            'currencies' => CurrencyCode::cases(),
        ]);
    }

    private function summaries(Collection $snapshots): Collection
    {
        $summaries = $this->aggregator->summarize($snapshots);

        return $this->aggregator->annotateDifferences($summaries);
    }
}
