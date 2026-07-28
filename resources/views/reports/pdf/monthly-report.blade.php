<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report — {{ $period->year }}-{{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        header { border-bottom: 2px solid #1f4e79; padding-bottom: 8px; margin-bottom: 16px; }
        h1 { font-size: 18px; margin: 0; color: #1f4e79; }
        h2 { font-size: 14px; margin-top: 20px; color: #1f4e79; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: right; }
        th { background: #d9e1f2; text-align: left; }
        td.label { text-align: left; }
        footer { position: fixed; bottom: 0; width: 100%; font-size: 9px; color: #666; border-top: 1px solid #ccc; padding-top: 4px; }
        .meta { color: #666; font-size: 10px; }
    </style>
</head>
<body>
<header>
    <h1>ArkaLedger — Monthly Financial Report</h1>
    <p class="meta">
        Period: {{ $period->year }}-{{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }} |
        PT. Arkananta |
        Generated: {{ $generatedAt->format('Y-m-d H:i') }}
    </p>
</header>

<h2>Consolidated P&amp;L Summary</h2>
<table>
    <thead>
    <tr>
        <th>Line Item</th>
        <th>Amount (IDR)</th>
    </tr>
    </thead>
    <tbody>
    @forelse($snapshot?->lines ?? [] as $line)
        <tr>
            <td class="label">{{ $line->pnlLine?->name ?? '—' }}</td>
            <td>{{ number_format((float) $line->amount, 2, '.', ',') }}</td>
        </tr>
    @empty
        <tr><td colspan="2" class="label">No consolidated snapshot available.</td></tr>
    @endforelse
    </tbody>
</table>

<h2>Per-Site Highlights</h2>
<table>
    <thead>
    <tr>
        <th>Site</th>
        <th>Name</th>
        <th>Profit / Loss</th>
    </tr>
    </thead>
    <tbody>
    @foreach($siteHighlights as $site)
        <tr>
            <td class="label">{{ $site['code'] }}</td>
            <td class="label">{{ $site['name'] }}</td>
            <td>{{ number_format($site['profit_loss'], 2, '.', ',') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<footer>
  Page <span class="page"></span> — ArkaLedger / FinSight — Confidential
</footer>
</body>
</html>
