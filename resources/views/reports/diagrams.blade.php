@extends('layouts.app')

@section('content')
    <section>
        <h2>Diagrams</h2>
        <p>Visual overview of total balances and monthly performance.</p>
        <div style="margin-bottom:2rem;">
            <canvas id="totalsChart" height="120"></canvas>
        </div>
        <div>
            <canvas id="monthlyChart" height="120"></canvas>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const totalCtx = document.getElementById('totalsChart');
        const totalsChart = new Chart(totalCtx, {
            type: 'line',
            data: {
                labels: @json($summaries->map(fn($s) => $s['date']->format('Y-m-d'))->values()),
                datasets: [{
                    label: 'Total (UAH)',
                    data: @json($summaries->map(fn($s) => $s['total_uah'])->values()),
                    borderColor: '#2563eb',
                    fill: false,
                    tension: 0.3,
                }]
            }
        });

        const reportData = @json($report);
        const months = Object.keys(reportData);
        const income = months.map((month) => reportData[month]?.income ?? 0);
        const spending = months.map((month) => reportData[month]?.spending ?? 0);

        const monthlyCtx = document.getElementById('monthlyChart');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Income (UAH)',
                        data: income,
                        backgroundColor: 'rgba(16,185,129,0.6)',
                    },
                    {
                        label: 'Spending (UAH)',
                        data: spending,
                        backgroundColor: 'rgba(248,113,113,0.7)',
                    }
                ]
            }
        });
    </script>
@endpush
