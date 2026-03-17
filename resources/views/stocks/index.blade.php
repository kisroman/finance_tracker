@extends('layouts.app')

@section('content')
    <section>
        <h2>Existing stocks</h2>
        <table class="stock-table" role="grid">
            <thead>
            <tr>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($stocks as $stock)
                <tr>
                    <td>
                        <input type="text" name="name" value="{{ $stock->name }}" required form="stock-update-{{ $stock->id }}">
                    </td>
                    <td class="stock-actions">
                        <button type="submit" form="stock-update-{{ $stock->id }}">Save</button>
                        <button type="submit"
                                form="stock-delete-{{ $stock->id }}"
                                class="secondary"
                                onclick="return confirm('Delete this stock? Related finance details will keep the reference.');">
                            Delete
                        </button>
                    </td>
                </tr>
                <form method="POST" action="{{ route('stocks.update', $stock) }}" id="stock-update-{{ $stock->id }}">
                    @csrf
                    @method('PUT')
                </form>
                <form method="POST" action="{{ route('stocks.destroy', $stock) }}" id="stock-delete-{{ $stock->id }}" class="sr-only">
                    @csrf
                    @method('DELETE')
                </form>
            @empty
                <tr>
                    <td colspan="3">No stocks yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section style="margin-top: 1.5rem;">
        <article>
            <h2>Add stock</h2>
            <form method="POST" action="{{ route('stocks.store') }}" class="add-stock-form">
                @csrf
                <label>
                    <span>Name</span>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <button type="submit">Create</button>
            </form>
        </article>
    </section>
@endsection

@push('styles')
    <style>
        .stock-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.5rem;
        }

        .stock-table thead th,
        .stock-table td {
            text-align: left;
            padding: 0.12rem 0.3rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #cbd5f5;
        }

        .stock-table td {
            text-transform: none;
            letter-spacing: normal;
            color: #e2e8f0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
        }

        .stock-table tbody tr:last-child td {
            border-bottom: none;
        }

        .stock-table input[type="text"] {
            width: 100%;
            padding: 0.08rem 0.25rem;
            height: 1.6rem;
            background: rgba(15, 23, 42, 0.35);
            border: 1px solid #1e293b;
            color: #f8fafc;
            font-size: 0.78rem;
        }

        .stock-actions {
            display: inline-flex;
            gap: 0.25rem;
            justify-content: center;
            align-items: center;
            width: 100%;
            min-width: 150px;
            white-space: nowrap;
        }

        .stock-actions button {
            min-width: 72px;
            height: 1.6rem;
            padding: 0;
            font-size: 0.75rem;
            background: #1d4ed8;
            border: none;
            color: #fff;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .stock-actions .secondary {
            background: #475569;
        }

        .stock-table thead th:last-child,
        .stock-table td.stock-actions {
            width: 190px;
        }

        .add-stock-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 0.5rem;
            align-items: center;
        }

        .add-stock-form label {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            color: #cbd5f5;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.75rem;
        }

        .add-stock-form input[type="text"] {
            padding: 0.1rem 0.3rem;
            height: 1.8rem;
            background: rgba(15, 23, 42, 0.35);
            border: 1px solid #1e293b;
            color: #f8fafc;
            font-size: 0.8rem;
        }

        .add-stock-form button {
            width: 100%;
            padding: 0.2rem 0.8rem;
            height: 1.8rem;
            font-size: 0.8rem;
            align-self: center;
            margin: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
