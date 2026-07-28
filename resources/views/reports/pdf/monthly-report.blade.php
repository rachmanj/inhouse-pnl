<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ArkaLedger Monthly Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        .footer { position: fixed; bottom: 0; font-size: 10px; color: #666; }
    </style>
</head>
<body>
    <h1>PT. Arkananta — Monthly P&L Report</h1>
    <p>Period: {{ $period->year }}-{{ str_pad($period->month, 2, '0', STR_PAD_LEFT) }}</p>
    <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>
    <table>
        <thead>
            <tr><th>Section</th><th>Amount (IDR)</th></tr>
        </thead>
        <tbody>
            <tr><td>Revenue Engineering</td><td>—</td></tr>
            <tr><td>Cost IPH</td><td>—</td></tr>
            <tr><td>Profit / Loss</td><td>—</td></tr>
        </tbody>
    </table>
    <div class="footer">ArkaLedger / FinSight — Confidential</div>
</body>
</html>
