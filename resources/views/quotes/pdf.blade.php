<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quotation {{ $tender->reference_no }}</title>
    <style>body{font-family:DejaVu Sans,sans-serif;color:#17221b;font-size:12px}h1{margin-bottom:4px}table{width:100%;border-collapse:collapse;margin-top:24px}th,td{padding:9px;border-bottom:1px solid #dce3d8;text-align:left}th{text-transform:uppercase;font-size:10px;color:#637168}.total{text-align:right;margin-top:20px;font-size:16px;font-weight:bold}</style>
</head>
<body>
    <h1>Quotation</h1>
    <div>Reference: {{ $tender->reference_no }}</div>
    <div>Customer: {{ $tender->creator?->name ?: 'Not linked' }}</div>
    <div>Email: {{ $tender->creator?->email ?: 'Not provided' }}</div>
    <div>Phone: {{ $tender->creator?->phone ?: 'Not provided' }}</div>
    <div>Position: {{ $tender->creator?->position ?: 'Not provided' }}</div>
    <div>Client / institution: {{ $tender->client_name ?: 'Not provided' }}</div>
    <div>Title: {{ $tender->title }}</div>
    <table>
        <thead><tr><th>Description</th><th>Unit</th><th>Qty</th><th>Unit price</th><th>Total</th></tr></thead>
        <tbody>
            @foreach ($tender->quoteItems->sortByDesc('version')->unique('description')->reverse() as $item)
                <tr><td>{{ $item->description }}</td><td>{{ $item->unit }}</td><td>{{ number_format((float) $item->quantity, 2) }}</td><td>{{ number_format((float) $item->unit_price, 2) }}</td><td>{{ number_format((float) $item->quantity * (float) $item->unit_price, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <div class="total">Total including VAT (18%): {{ $tender->currency }} {{ number_format((float) $tender->quoted_amount, 2) }}</div>
</body>
</html>
