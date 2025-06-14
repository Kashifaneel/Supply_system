<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .customer-info {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .total-section {
            width: 300px;
            margin-left: auto;
            border: 1px solid #ddd;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
        }
        .signature {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="invoice-info">
        <div>
            <strong>Invoice No:</strong> INV-{{ $supply->id }}-{{ $supply->supply_date->format('Ymd') }}<br>
            <strong>Invoice Date:</strong> {{ $supply->supply_date->format('d/m/Y') }}<br>
            <strong>PO No:</strong> {{ $supply->purchaseOrder->po_number }}<br>
            <strong>PO Date:</strong> {{ $supply->purchaseOrder->po_date->format('d/m/Y') }}
        </div>
        <div>
            <strong>Supply Date:</strong> {{ $supply->supply_date->format('d/m/Y') }}<br>
            <strong>DC No:</strong> DC-{{ $supply->id }}-{{ $supply->supply_date->format('Ymd') }}
        </div>
    </div>

    <div class="customer-info">
        <h3>Bill To:</h3>
        <strong>{{ $supply->purchaseOrder->institution_name }}</strong><br>
        @if($supply->purchaseOrder->institution_address)
            {{ $supply->purchaseOrder->institution_address }}<br>
        @endif
        @if($supply->purchaseOrder->institution_email)
            Email: {{ $supply->purchaseOrder->institution_email }}<br>
        @endif
        @if($supply->purchaseOrder->institution_phone)
            Phone: {{ $supply->purchaseOrder->institution_phone }}
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Description</th>
                <th>Batch No</th>
                <th>Quantity</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supply->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>
                    {{ $item->poItem->name }}
                    @if($item->poItem->mfg_date || $item->poItem->exp_date)
                        <br><small>
                            @if($item->poItem->mfg_date)
                                Mfg: {{ $item->poItem->mfg_date->format('d/m/Y') }}
                            @endif
                            @if($item->poItem->exp_date)
                                Exp: {{ $item->poItem->exp_date->format('d/m/Y') }}
                            @endif
                        </small>
                    @endif
                </td>
                <td>{{ $item->poItem->batch_no ?? '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>PKR {{ number_format($item->poItem->price, 2) }}</td>
                <td>PKR {{ number_format($item->quantity * $item->poItem->price, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table style="margin: 0;">
            <tr>
                <td style="text-align: right; padding: 5px;"><strong>Subtotal:</strong></td>
                <td style="text-align: right; padding: 5px;">PKR {{ number_format($supply->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding: 5px;"><strong>Tax (0%):</strong></td>
                <td style="text-align: right; padding: 5px;">PKR 0.00</td>
            </tr>
            <tr class="total-row">
                <td style="text-align: right; padding: 5px;"><strong>Total Amount:</strong></td>
                <td style="text-align: right; padding: 5px;"><strong>PKR {{ number_format($supply->total_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p><strong>Payment Terms:</strong> As per agreement</p>
        <p><strong>Note:</strong> This is a computer generated invoice.</p>
    </div>

    <div class="signature">
        <div>
            <p>_____________________</p>
            <p>Customer Signature</p>
        </div>
        <div>
            <p>_____________________</p>
            <p>Authorized Signatory</p>
        </div>
    </div>
</body>
</html>
