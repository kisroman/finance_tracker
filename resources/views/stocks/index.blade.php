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
            border-collapse: separate;
            border-spacing: 0 0.35rem;
        }

        .stock-table thead th {
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #cbd5f5;
        }

        .stock-table tbody tr {
            background: linear-gradient(145deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.92) 100%);
            box-shadow: 0 4px 8px rgba(2, 6, 23, 0.35);
            border: 1px solid #1f2937;
        }

        .stock-table td {
            padding: 0.2rem 0.35rem;
            vertical-align: middle;
        }

        .stock-table input[type="text"] {
            width: 100%;
            padding: 0.2rem 0.35rem;
            background: #0f172a;
            border: 1px solid #1e293b;
            color: #f8fafc;
            font-size: 0.85rem;
        }

        .stock-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.2rem;
        }

        .stock-actions button {
            width: 100%;
            padding: 0.25rem 0.4rem;
            font-size: 0.8rem;
            background: #1d4ed8;
            border: none;
            color: #fff;
        }

        .stock-actions .secondary {
            background: #475569;
        }

        .add-stock-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.5rem;
        }

        .add-stock-form label {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
            color: #cbd5f5;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.8rem;
        }

        .add-stock-form input[type="text"] {
            padding: 0.25rem 0.4rem;
            background: #0f172a;
            border: 1px solid #1e293b;
            color: #f8fafc;
            font-size: 0.9rem;
        }

        .add-stock-form button {
            width: auto;
            padding: 0.35rem 0.9rem;
            font-size: 0.9rem;
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
