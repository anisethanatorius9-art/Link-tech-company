<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 28px 26px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #17221b;
            font-size: 11px;
        }

        .brand {
            color: #1d27d8;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 1px;
        }

        .contact {
            text-align: center;
            line-height: 1.45;
        }

        .meta {
            margin-top: 22px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #17221b;
            padding: 7px 6px;
        }

        th {
            background: #edf0ef;
            text-align: center;
        }

        .number {
            text-align: right;
        }

        .summary {
            width: 52%;
            margin-left: auto;
        }

        .summary td {
            font-weight: bold;
            background: #edf0ef;
        }

        .terms {
            margin-top: 18px;
            line-height: 1.55;
        }

        .terms strong {
            display: block;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .footer {
            margin-top: 26px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div style="text-align:center"><img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('link-tech-logo.svg'))) }}" style="width:58px;height:58px">
        <div class="brand">LINK-TECH COMPANY LIMITED</div>
    </div>
    <div class="contact">Bagamoyo Road, Mbezi Park-Mbezi Beach/Plot No.555/C/1st Floor<br>P.O. Box 33225, Dar es Salaam<br>Mob: +255 717007797 / +255 787255353<br>info@linktech.co.tz | sales@linktech.co.tz | www.linktech.co.tz<br><strong>TIN NO. 106-215-448 &nbsp;&nbsp;&nbsp; VRN 40-019588-0</strong></div>
    <div class="meta"><strong>Name:</strong> VICE PRESIDENT OFFICE<br><strong>Address:</strong> P.O. Box 2502<br><strong>City:</strong> Dodoma<br><strong>Date:</strong> {{ now()->format('j/n/Y') }}<br><strong>Quotation:</strong> QTNN0002/D1/M09/{{ now()->format('Y') }}</div>
    <table>
        <thead>
            <tr>
                <th>S/N</th>
                <th>PRODUCT DESCRIPTION</th>
                <th>QTY</th>
                <th>UNIT PRICE (TSH)</th>
                <th>TOTAL AMOUNT (TSH)</th>
            </tr>
        </thead>
        <tbody>
            @php($subtotal = 0)
            @foreach ($requests as $index => $request)
            @php($quantity = (int) ($request->quantity ?: 1))
            @php($lineTotal = $quantity * (float) ($request->unit_price ?: 0))
            @php($subtotal += $lineTotal)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $request->title }}@if($request->notes) <br>{{ $request->notes }} @endif</td>
                <td>{{ $quantity }}</td>
                <td class="number">{{ number_format((float) ($request->unit_price ?: 0), 2) }}</td>
                <td class="number">{{ number_format($lineTotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @php($vatPercent = (float) ($requests->first()?->vat_percent ?: 18))
    @php($vat = $subtotal * $vatPercent / 100)
    <table class="summary">
        <tr>
            <td>Subtotal (Tsh.)</td>
            <td class="number">{{ number_format($subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>VAT {{ number_format($vatPercent, 0) }}%</td>
            <td class="number">{{ number_format($vat, 2) }}</td>
        </tr>
        <tr>
            <td>Grand total (Tsh.)</td>
            <td class="number">{{ number_format($subtotal + $vat, 2) }}</td>
        </tr>
    </table>
    <div class="terms"><strong>TERMS &amp; CONDITIONS</strong>Payment: Cheque/Cash<br>Payment terms: Within 30 days after delivery<br>Delivery: Within 7 days after receiving LPO<br>Goods remain the property of LINK-TECH CO. LTD until paid for in full<br>Overdue accounts will accrue interest 15% per month<br>Goods once sold cannot be taken back<br>One-year limited warranty, return to authorized service center.</div>
    <div class="footer"><span>Sales manager: Gilta Makundi</span><span>Signature</span></div>
</body>

</html>