<?php

namespace App\Console\Commands;

use App\Models\FinanceSnapshot;
use App\Models\Stock;
use Illuminate\Console\Command;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ImportLegacyFinance extends Command
{
    /**
     * @var string
     */
    protected $signature = 'finance:import-legacy {--truncate : Remove current finance data before importing}';

    /**
     * @var string
     */
    protected $description = 'Import finance history from the legacy finance_test schema into the new structure.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('truncate')) {
            $this->truncateCurrentData();
        }

        $legacyStocks = DB::table($this->legacyTable('stock'))
            ->orderBy('id')
            ->get();

        $stockMap = $this->importStocks($legacyStocks);

        $financeGroups = DB::table($this->legacyTable('finance'))
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->groupBy('date');

        [$snapshots, $details] = $this->importFinanceDetails($financeGroups, $stockMap);

        $this->info(sprintf('Imported %d snapshots and %d finance details.', $snapshots, $details));

        return self::SUCCESS;
    }

    private function truncateCurrentData(): void
    {
        $this->warn('Truncating finance tables...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('finance_details')->truncate();
        DB::table('finance_snapshots')->truncate();
        DB::table('stocks')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * @param \Illuminate\Support\Collection<int, object> $legacyStocks
     * @return array<string, int>
     */
    private function importStocks(Collection $legacyStocks): array
    {
        $this->info(sprintf('Syncing %d legacy stock records...', $legacyStocks->count()));

        $map = [];

        foreach ($legacyStocks as $legacyStock) {
            $name = trim((string) ($legacyStock->name ?? ''));

            if ($name === '') {
                continue;
            }

            $stock = Stock::firstOrCreate(
                ['name' => $name],
                ['is_active' => true],
            );

            $map[$name] = $stock->id;
        }

        return $map;
    }

    /**
     * @param \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, object>> $financeGroups
     * @param array<string, int> $stockMap
     * @return array{int, int}
     */
    private function importFinanceDetails(Collection $financeGroups, array &$stockMap): array
    {
        $snapshotCount = 0;
        $detailCount = 0;

        DB::transaction(function () use ($financeGroups, &$stockMap, &$snapshotCount, &$detailCount) {
            foreach ($financeGroups as $date => $rows) {
                /** @var string $date */
                $snapshot = FinanceSnapshot::create([
                    'snapshot_date' => $date,
                    'note' => $this->noteFromRows($rows),
                ]);

                $snapshotCount++;
                $position = 1;

                foreach ($rows as $row) {
                    $stockName = trim((string) ($row->stock ?? ''));

                    if ($stockName !== '' && ! isset($stockMap[$stockName])) {
                        $stockMap[$stockName] = Stock::create([
                            'name' => $stockName,
                            'is_active' => true,
                        ])->id;
                    }

                    $snapshot->details()->create([
                        'stock_id' => $stockMap[$stockName] ?? null,
                        'source' => (string) ($row->source ?? ''),
                        'amount' => round((float) ($row->sum ?? 0), 2),
                        'currency_code' => $this->currencyCode($row->currency ?? ''),
                        'is_active' => (bool) ($row->active ?? false),
                        'comment' => $this->normalizeComment($row->comment ?? null),
                        'position' => $position++,
                    ]);

                    $detailCount++;
                }
            }
        });

        return [$snapshotCount, $detailCount];
    }

    private function noteFromRows(Collection $rows): ?string
    {
        $note = $rows->pluck('comment')
            ->filter()
            ->map(fn ($comment) => trim((string) $comment))
            ->filter()
            ->unique()
            ->implode(', ');

        return $note === '' ? null : $note;
    }

    private function currencyCode(string $raw): string
    {
        $code = strtoupper(substr(trim($raw), 0, 3));

        return $code !== '' ? $code : config('services.currency_rates.base_currency', 'UAH');
    }

    private function normalizeComment(?string $comment): ?string
    {
        $trimmed = trim((string) $comment);

        return $trimmed === '' ? null : $trimmed;
    }

    private function legacyTable(string $table): Expression
    {
        return DB::raw(sprintf('`finance_test`.`%s`', $table));
    }
}
