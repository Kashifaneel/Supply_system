<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Challan</title>
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
        .company-info {
            margin-bottom: 20px;
        }
        .dc-info {
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
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
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
        <h1>DELIVERY CHALLAN</h1>
        <p>{{ config('app.name') }}</p>
    </div>

    <div class="dc-info">
        <div>
            <strong>DC No:</strong> DC-{{ $supply->id }}-{{ $supply->supply_date->format('Ymd') }}<br>
            <strong>DC Date:</strong> {{ $supply->supply_date->format('d/m/Y') }}<br>
            <strong>PO No:</strong> {{ $supply->purchaseOrder->po_number }}
        </div>
        <div>
            <strong>Supply Date:</strong> {{ $supply->supply_date->format('d/m/Y') }}<br>
            <strong>Supplied By:</strong> {{ $supply->user->name }}
        </div>
    </div>

    <div class="customer-info">
        <h3>Delivery To:</h3>
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
                <th>Item Name</th>
                <th>Batch No</th>
                <th>Mfg Date</th>
                <th>Exp Date</th>
                <th>Quantity Supplied</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($supply->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->poItem->name }}</td>
                <td>{{ $item->poItem->batch_no ?? '-' }}</td>
                <td>{{ $item->poItem->mfg_date ? $item->poItem->mfg_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->poItem->exp_date ? $item->poItem->exp_date->format('d/m/Y') : '-' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>PKR {{ number_format($item->poItem->price, 2) }}</td>
                <td>PKR {{ number_format($item->quantity * $item->poItem->price, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="7" style="text-align: right;"><strong>Total Amount:</strong></td>
                <td><strong>PKR {{ number_format($supply->total_amount, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p><strong>Terms & Conditions:</strong></p>
        <ul style="text-align: left; font-size: 10px;">
            <li>Goods once delivered will not be taken back.</li>
            <li>All disputes are subject to local jurisdiction.</li>
            <li>Payment terms as per agreement.</li>
        </ul>
    </div>

    <div class="signature">
        <div>
            <p>_____________________</p>
            <p>Receiver's Signature</p>
        </div>
        <div>
            <p>_____________________</p>
            <p>Authorized Signatory</p>
        </div>
    </div>
</body>
</html>
