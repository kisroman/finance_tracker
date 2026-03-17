@extends('layouts.app')

@section('content')
    <section class="grid">
        <article>
            <h2>Add stock</h2>
            <form method="POST" action="{{ route('stocks.store') }}">
                @csrf
                <label>Name
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </label>
                <label class="toggle-field">
                    <span>Active?</span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked>
                </label>
                <button type="submit">Create</button>
            </form>
        </article>
        <article>
            <p>Stocks describe where a finance detail belongs (bank, broker, etc.). Toggle the active flag to hide rarely used entries from forms.</p>
        </article>
    </section>

    <section>
        <h2>Existing stocks</h2>
        <table role="grid">
            <thead>
            <tr>
                <th>Name</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($stocks as $stock)
                <tr>
                    <td colspan="3">
                        <form method="POST" action="{{ route('stocks.update', $stock) }}" class="grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 0.5rem; align-items: end;">
                            @csrf
                            @method('PUT')
                            <label>Name
                                <input type="text" name="name" value="{{ $stock->name }}" required>
                            </label>
                            <label class="toggle-field">
                                <span>Active?</span>
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($stock->is_active)>
                            </label>
                            <button type="submit">Save</button>
                        </form>
                        <form method="POST" action="{{ route('stocks.destroy', $stock) }}" class="inline" onsubmit="return confirm('Delete this stock? Related finance details will keep the reference.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="secondary">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No stocks yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
