<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        body { background: #f6f7fb; }
        header { background: #101727; color: #fff; }
        header nav a { color: #fff; margin-right: 1rem; }
        table tbody tr { cursor: pointer; }
        table tbody tr:hover { background: #f1f5f9; }
        .status { padding: 0.75rem 1rem; border-radius: 6px; background: #e0f7ec; color: #065f46; margin-bottom: 1rem; }
        .danger { background: #fee2e2; color: #b91c1c; }
        form.inline { display: inline; }
        .detail-form input[type="text"],
        .detail-form input[type="number"],
        .detail-form select { width: 100%; }
        .detail-form button { width: 100%; }
        .text-success { color: #047857; }
        .text-danger { color: #b91c1c; }
    </style>
</head>
<body>
@if(!empty($headerRates))
    <div style="background:#e0e7ff; color:#1e3a8a; padding:0.5rem 0;">
        <div class="container" style="display:flex; gap:1rem; font-size:0.9rem;">
            <span><strong>Rates ({{ config('services.currency_rates.base_currency') }} base):</strong></span>
            <span>1 USD = {{ number_format($headerRates['USD'], 2) }} {{ config('services.currency_rates.base_currency') }}</span>
            <span>1 EUR = {{ number_format($headerRates['EUR'], 2) }} {{ config('services.currency_rates.base_currency') }}</span>
        </div>
    </div>
@endif
<header class="container">
    <nav>
        <ul>
            <li><strong>{{ config('app.name') }}</strong></li>
        </ul>
        <ul>
            <li><a href="{{ route('snapshots.index') }}">Finances</a></li>
            <li><a href="{{ route('reports.spending-income') }}">Monthly report</a></li>
            <li><a href="{{ route('reports.diagrams') }}">Diagrams</a></li>
            <li><a href="{{ route('stocks.index') }}">Stocks</a></li>
        </ul>
    </nav>
</header>
<main class="container">
    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="status danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
