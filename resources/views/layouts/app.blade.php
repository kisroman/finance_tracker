<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        body { back
        header { background: #101727; color: #fff; }
        header nav a { color: #fff; margin-right: 1rem; }
        header nav ul:first-of-type { padding-left: 1rem; }
        .container { max-width: none; width: 100%; margin: 0; }
        main section { margin-block: 0; padding: 0.5rem 0; }
        section h2 {
            margin: 0 0 0.5rem;
            background: #101727;
            color: #fff;
            padding: 0.35rem 0.6rem;
            border-radius: 6px;        }
        .grid > article { margin: 0; }
        table th,
        table td { padding: 0.18rem 0.255rem; }
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
        .snapshot-form {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: nowrap;
            font-size: 0.9rem;
        }
        .snapshot-form input[type="date"],
        .snapshot-form button {
            width: auto;
            font-size: 0.9rem;
            padding: 0.4rem 0.8rem;
        }
        table button.secondary {
            padding: 0.2rem 0.6rem;
            font-size: 0.85rem;
            margin: 0;
        }
        .toggle-field {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 0.5rem;
            max-width: 220px;
        }
        .toggle-field span { white-space: nowrap; }
        .toggle-field input[type="checkbox"] {
            width: 1.5rem;
            height: 1.5rem;
        }
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
            <li><a href="{{ route('snapshots.index') }}"><strong>{{ config('app.name') }}</strong></a></li>
        </ul>
        <ul>
            <li><a href="{{ route('snapshots.index') }}">Finances</a></li>
            <li><a href="{{ route('reports.spending-income') }}">Monthly report</a></li>
            <li><a href="{{ route('reports.diagrams') }}">Diagrams</a></li>
            <li><a href="{{ route('stocks.index') }}">Stocks</a></li>
        </ul>
    </nav>
</header>
<main class="container" style="padding:0;">
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
