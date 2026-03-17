@extends('layouts.app')

@section('content')
    <section class="grid">
        <article>
            <h2>Snapshot {{ $snapshot->snapshot_date->format('Y-m-d') }}</h2>
            @if($snapshot->note)
                <p>{{ $snapshot->note }}</p>
            @endif
            <a href="{{ route('snapshots.index') }}">Back to overview</a>
        </article>
    </section>

    <section>
        <h2>Finance details</h2>
        @forelse($snapshot->details as $detail)
            <article class="detail-form" style="border:1px solid #e2e8f0; padding:1rem; border-radius:8px; margin-bottom:1rem;">
                <form method="POST" action="{{ route('details.update', $detail) }}" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap:1rem;" id="detail-update-{{ $detail->id }}">
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
                    <label class="toggle-field">
                        <span>Active?</span>
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" @checked($detail->is_active)>
                    </label>
                    <div style="display:flex; gap:0.5rem; flex-wrap:wrap; align-items:center;">
                        <button type="submit">Save</button>
                        <button type="submit"
                                form="detail-delete-{{ $detail->id }}"
                                class="secondary"
                                onclick="return confirm('Delete this row?');">
                            Delete
                        </button>
                    </div>
                </form>
                <form method="POST" action="{{ route('details.destroy', $detail) }}" id="detail-delete-{{ $detail->id }}" style="display:none;">
                    @csrf
                    @method('DELETE')
                </form>
            </article>
        @empty
            <p>No rows yet.</p>
        @endforelse
    </section>

    <section style="margin-top:2rem;">
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
                <label class="toggle-field">
                    <span>Is active?</span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked>
                </label>
                <button type="submit">Add detail</button>
            </form>
        </article>
    </section>
@endsection
