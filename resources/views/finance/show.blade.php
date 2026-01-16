@extends('layouts.app')

@section('content')
    <section class="grid">
        <article>
            <h2>Snapshot {{ $snapshot->snapshot_date->format('Y-m-d') }}</h2>
            @if($snapshot->note)
                <p>{{ $snapshot->note }}</p>
            @endif
            <ul>
                <li>Total (UAH): <strong>{{ number_format($summary['total_uah'], 2) }}</strong></li>
                <li>Active (UAH): <strong>{{ number_format($summary['active_total_uah'], 2) }}</strong></li>
                <li>Total (USD): <strong>{{ number_format($summary['total_usd'], 2) }}</strong></li>
                <li>Active (USD): <strong>{{ number_format($summary['active_total_usd'], 2) }}</strong></li>
            </ul>
            <a href="{{ route('snapshots.index') }}">Back to overview</a>
        </article>
        <article>
            <h2>Add detail</h2>
            <form class="detail-form" method="POST" action="{{ route('snapshots.details.store', $snapshot) }}">
                @csrf
                <label>Stock
                    <select name="stock_id">
                        <option value="">Select stock</option>
                        @foreach($stocks as $stock)
                            <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Source
                    <input type="text" name="source" required>
                </label>
                <label>Sum
                    <input type="number" name="amount" step="0.01" required>
                </label>
                <label>Currency
                    <select name="currency_code">
                        @foreach($currencies as $currency)
                            <option value="{{ $currency->value }}">{{ $currency->value }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="grid">
                    <span>Is active?</span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked>
                </label>
                <label>Comment
                    <input type="text" name="comment">
                </label>
                <button type="submit">Add detail</button>
            </form>
        </article>
    </section>

    <section>
        <h2>Finance details</h2>
        @forelse($snapshot->details as $detail)
            <article class="detail-form" style="border:1px solid #e2e8f0; padding:1rem; border-radius:8px; margin-bottom:1rem;">
                <form method="POST" action="{{ route('details.update', $detail) }}" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1rem;">
                    @csrf
                    @method('PUT')
                    <label>Stock
                        <select name="stock_id">
                            <option value="">-</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}" @selected($detail->stock_id === $stock->id)>{{ $stock->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Source
                        <input type="text" name="source" value="{{ $detail->source }}" required>
                    </label>
                    <label>Sum
                        <input type="number" step="0.01" name="amount" value="{{ $detail->amount }}" required>
                    </label>
                    <label>Currency
                        <select name="currency_code">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->value }}" @selected($detail->currency_code === $currency->value)>{{ $currency->value }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="grid">
                        <span>Active?</span>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked($detail->is_active)>
                    </label>
                    <label>Comment
                        <input type="text" name="comment" value="{{ $detail->comment }}">
                    </label>
                    <label>Position
                        <input type="number" name="position" value="{{ $detail->position }}" min="0">
                    </label>
                    <div>
                        <button type="submit">Save</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('details.destroy', $detail) }}" class="inline" onsubmit="return confirm('Delete this row?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="secondary">Delete</button>
                </form>
            </article>
        @empty
            <p>No rows yet.</p>
        @endforelse
    </section>
@endsection
