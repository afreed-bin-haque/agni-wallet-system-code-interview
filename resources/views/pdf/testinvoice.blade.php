<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Test Invoice</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        h1 {
            color: #0f172a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background: #f1f5f9;
        }
    </style>
</head>

<body>

    <h1>Invoice #TEST-001</h1>
    <p>Date: {{ now()->format('Y-m-d') }}</p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Sample Product</td>
                <td>2</td>
                <td>500 BDT</td>
            </tr>
        </tbody>
    </table>

    <p><strong>Total: 1000 BDT</strong></p>

</body>

</html>