<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(): View
    {
        $stocks = Stock::orderBy('name')->get();

        return view('stocks.index', [
            'stocks' => $stocks,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        Stock::create($data);

        return redirect()->route('stocks.index')->with('status', 'Stock created.');
    }

    public function update(Request $request, Stock $stock): RedirectResponse
    {
        $data = $this->validatedData($request, $stock->id);
        $stock->update($data);

        return redirect()->route('stocks.index')->with('status', 'Stock updated.');
    }

    public function destroy(Stock $stock): RedirectResponse
    {
        $stock->delete();

        return redirect()->route('stocks.index')->with('status', 'Stock removed.');
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $request->merge([
            'is_active' => $request->boolean('is_active'),
        ]);

        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('stocks', 'name')->ignore($ignoreId),
            ],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
