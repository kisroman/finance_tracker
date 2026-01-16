<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFinanceDetailRequest;
use App\Http\Requests\UpdateFinanceDetailRequest;
use App\Models\FinanceDetail;
use App\Models\FinanceSnapshot;
use Illuminate\Http\RedirectResponse;

class FinanceDetailController extends Controller
{
    public function store(StoreFinanceDetailRequest $request, FinanceSnapshot $snapshot): RedirectResponse
    {
        $payload = $this->payload($request->validated(), $request->boolean('is_active'));

        if (! isset($payload['position'])) {
            $payload['position'] = (int) $snapshot->details()->max('position') + 1;
        }

        $snapshot->details()->create($payload);

        return redirect()->route('snapshots.show', $snapshot)
            ->with('status', 'Finance detail added.');
    }

    public function update(UpdateFinanceDetailRequest $request, FinanceDetail $detail): RedirectResponse
    {
        $detail->update($this->payload($request->validated(), $request->boolean('is_active')));

        return back()->with('status', 'Finance detail updated.');
    }

    public function destroy(FinanceDetail $detail): RedirectResponse
    {
        $snapshot = $detail->snapshot;
        $detail->delete();

        return redirect()->route('snapshots.show', $snapshot)
            ->with('status', 'Finance detail removed.');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function payload(array $data, bool $isActive): array
    {
        if (array_key_exists('currency_code', $data)) {
            $data['currency_code'] = strtoupper($data['currency_code']);
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = $isActive;
        }

        return $data;
    }
}
