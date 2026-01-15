<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Agni Transaction Statement</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #0f172a;
        }

        h1 {
            font-size: 20px;
            margin-bottom: 5px;
        }

        .sub {
            color: #475569;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            font-weight: 600;
            width: 35%;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .credit {
            background: #dcfce7;
            color: #166534;
        }

        .debit {
            background: #fee2e2;
            color: #991b1b;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            color: #64748b;
            text-align: center;
        }
    </style>
</head>

<body>

    <h1>Transaction Statement</h1>
    <div class="sub">
        Generated on {{ now()->format('Y-m-d h:i A') }}
    </div>

    <table>
        <tr>
            <th>Wallet Transaction ID</th>
            <td>{{ $trx->wallet_trx_id }}</td>
        </tr>
        <tr>
            <th>Wallet ID</th>
            <td>{{ $trx->wallet_id }}</td>
        </tr>
        <tr>
            <th>Transaction Type</th>
            <td>
                <span class="badge {{ $trx->type === 'credit' ? 'credit' : 'debit' }}">
                    {{ ucfirst($trx->type) }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Amount</th>
            <td>{{ number_format($trx->amount, 2) }} BDT</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>{{ $trx->phone }}</td>
        </tr>
        <tr>
            <th>Payment ID</th>
            <td>{{ $trx->payment_id }}</td>
        </tr>
        <tr>
            <th>TRX ID</th>
            <td>{{ $trx->trx_id }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <span class="badge success">
                    {{ $trx->bkash_res['statusMessage'] ?? ucfirst($trx->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <th>Transaction Date</th>
            <td>
                {{ \Carbon\Carbon::parse($trx->created_at)->timezone('Asia/Dhaka')->format('Y-m-d h:i A') }}
            </td>
        </tr>
    </table>

    <div class="footer">
        This is a system generated transaction statement from Agni Wallet.
    </div>

</body>

</html>
