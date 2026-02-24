@extends('layouts.app')

@section('content')
    <section class="grid">
        <article>
            <h2>Add finance snapshot</h2>
            <p>Pick a date to duplicate the latest details and adjust only what changed.</p>
            <form method="POST" action="{{ route('snapshots.store') }}">
                @csrf
                <label for="snapshot_date">Date</label>
                <input type="date" id="snapshot_date" name="snapshot_date" value="{{ old('snapshot_date', now()->toDateString()) }}" required>
                <button type="submit">Create snapshot</button>
            </form>
        </article>
    </section>

    <section>
        <h2>Snapshots</h2>
        <table role="grid">
            <thead>
            <tr>
                <th>Date</th>
                <th>Total (UAH)</th>
                <th>Active (UAH)</th>
                <th>Total (USD)</th>
                <th>Active (USD)</th>
                <th>Diff vs. previous</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($summaries as $summary)
                @php($diff = $summary['difference'] ?? 0)
                @php($canDelete = $summary['date']->isSameMonth(now()))
                <tr onclick="window.location='{{ route('snapshots.show', $summary['snapshot']) }}'">
                    <td>{{ $summary['date']->format('Y-m-d') }}</td>
                    <td>{{ number_format($summary['total_uah'], 2) }}</td>
                    <td>{{ number_format($summary['active_total_uah'], 2) }}</td>
                    <td>{{ number_format($summary['total_usd'], 2) }}</td>
                    <td>{{ number_format($summary['active_total_usd'], 2) }}</td>
                    <td class="{{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 2) }}
                    </td>
                    <td>
                        @if($canDelete)
                            <form method="POST"
                                  action="{{ route('snapshots.destroy', $summary['snapshot']) }}"
                                  onsubmit="event.stopPropagation(); return confirm('Delete this snapshot?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="secondary" onclick="event.stopPropagation();">Delete</button>
                            </form>
                        @else
                            &mdash;
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">No snapshots yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
