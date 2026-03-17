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
        <table class="details-table" role="grid">
            <thead>
            <tr>
                <th>Stock</th>
                <th>Source</th>
                <th>Sum</th>
                <th>Currency</th>
                <th>Active?</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($snapshot->details as $detail)
                <tr>
                    <td class="detail-static">{{ $detail->stock?->name ?? '—' }}</td>
                    <td>
                        <input type="text" name="source" value="{{ $detail->source }}" required form="detail-update-{{ $detail->id }}">
                    </td>
                    <td>
                        <input type="number" name="amount" step="1" value="{{ (int) round($detail->amount) }}" required form="detail-update-{{ $detail->id }}">
                    </td>
                    <td>
                        <select name="currency_code" form="detail-update-{{ $detail->id }}">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->value }}" @selected($detail->currency_code === $currency->value)>{{ $currency->value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="toggle-cell">
                        <input type="hidden" name="is_active" value="0" form="detail-update-{{ $detail->id }}">
                        <input type="checkbox" name="is_active" value="1" @checked($detail->is_active) form="detail-update-{{ $detail->id }}">
                    </td>
                    <td>
                        <div class="detail-actions">
                            <button type="submit" form="detail-update-{{ $detail->id }}">Save</button>
                            <button type="submit"
                                    form="detail-delete-{{ $detail->id }}"
                                    class="secondary"
                                    onclick="return confirm('Delete this row?');">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                <form method="POST" action="{{ route('details.update', $detail) }}" id="detail-update-{{ $detail->id }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="stock_id" value="{{ $detail->stock_id }}">
                </form>
                <form method="POST" action="{{ route('details.destroy', $detail) }}" id="detail-delete-{{ $detail->id }}" class="sr-only">
                    @csrf
                    @method('DELETE')
                </form>
            @empty
                <tr>
                    <td colspan="6">No rows yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section style="margin-top:2rem;">
        <article>
            <h2>Add detail</h2>
            <form id="detail-create" method="POST" action="{{ route('snapshots.details.store', $snapshot) }}" class="sr-only">
                @csrf
            </form>
            <table class="details-table" role="grid">
                <thead>
                <tr>
                    <th>Stock</th>
                    <th>Source</th>
                    <th>Sum</th>
                    <th>Currency</th>
                    <th>Active?</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <select name="stock_id" form="detail-create">
                            <option value="">Select stock</option>
                            @foreach($stocks as $stock)
                                <option value="{{ $stock->id }}">{{ $stock->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <input type="text" name="source" form="detail-create" required>
                    </td>
                    <td>
                        <input type="number" name="amount" step="1" form="detail-create" required>
                    </td>
                    <td>
                        <select name="currency_code" form="detail-create">
                            @foreach($currencies as $currency)
                                <option value="{{ $currency->value }}">{{ $currency->value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="toggle-cell">
                        <input type="hidden" name="is_active" value="0" form="detail-create">
                        <input type="checkbox" name="is_active" value="1" checked form="detail-create">
                    </td>
                    <td>
                        <div class="detail-actions detail-actions--single">
                            <button type="submit" form="detail-create">Add detail</button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </article>
    </section>
@endsection

@push('styles')
    <style>
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .details-table th,
        .details-table td {
            padding: 0.2rem 0.35rem;
            text-align: left;
            font-size: 0.85rem;
            vertical-align: middle;
        }

        .details-table thead th {
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .details-table tbody tr {
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        }

        .details-table tbody tr:last-child {
            border-bottom: none;
        }

        .details-table input,
        .details-table select {
            width: 100%;
            padding: 0.18rem 0.3rem;
            font-size: 0.82rem;
            background: rgba(15, 23, 42, 0.35);
            border: 1px solid #1e293b;
            color: #f8fafc;
            border-radius: 4px;
            margin-bottom: 0;
        }

        .details-table button {
            margin-bottom: 0;
        }

        .detail-static {
            font-weight: 600;
        }

        .toggle-cell {
            position: relative;
            padding: 0;
        }

        .toggle-cell input[type="checkbox"] {
            width: 1rem;
            height: 1rem;
            margin: 0;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .detail-actions {
            display: inline-flex;
            gap: 0.25rem;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .detail-actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 32px;
            padding: 0;
            font-size: 0.8rem;
            background: #1d4ed8;
            border: none;
            color: #fff;
        }

        .detail-actions .secondary {
            background: #475569;
        }

        .detail-actions--single button {
            width: 100%;
        }

        .details-table tbody tr:hover {
            background-color: inherit;
        }

        .sr-only {
            position: absolute;
            left: -9999px;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }
    </style>
@endpush
