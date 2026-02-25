@extends('layouts.app')

@section('content')
    <section>
        <h2>Monthly spending vs income</h2>
        <table role="grid">
            <thead>
            <tr>
                <th>Month</th>
                <th>Income (UAH)</th>
                <th>Spending (UAH)</th>
                <th>Net</th>
            </tr>
            </thead>
            <tbody>
            @forelse($report as $month => $data)
                @php($net = ($data['income'] ?? 0) - ($data['spending'] ?? 0))
                <tr>
                    <td>{{ $month }}</td>
                    <td>{{ number_format($data['income'] ?? 0, 2) }}</td>
                    <td>{{ number_format($data['spending'] ?? 0, 2) }}</td>
                    <td class="{{ $net >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $net >= 0 ? '+' : '' }}{{ number_format($net, 2) }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No data yet.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </section>
@endsection
