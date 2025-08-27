<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Catering Management | Monthly Invoice</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f5f7;
            color: #2d3436;
            margin: 0;
            padding: 0;
        }

        .invoice-container {
            max-width: 640px;
            margin: 30px auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .invoice-header {
            background-color: #2a9d8f;
            color: #fff;
            padding: 20px 30px;
        }

        .invoice-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .invoice-body {
            padding: 30px;
        }

        .invoice-body p {
            margin-bottom: 10px;
        }

        .invoice-details {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
        }

        .invoice-details th,
        .invoice-details td {
            border: 1px solid #ddd;
            padding: 12px 15px;
        }

        .invoice-details th {
            background-color: #f1f1f1;
            text-align: left;
        }

        .highlight {
            font-weight: bold;
            font-size: 14px;
            /* Changed from 18px to 14px */
            color: #e76f51;
            text-align: right;
            margin-top: 20px;
        }

        .invoice-footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <h2>Catering Management System</h2>
        </div>
        <div class="invoice-body">
            <p>Dear <strong>{{ $invoice->employee->name ?? 'Customer' }}</strong>,</p>
            <p>Below is your invoice summary for <strong>{{ \Carbon\Carbon::create($invoice->year, $invoice->month)->format('F Y') }}</strong>.</p>

            <table class="invoice-details">
                <tr>
                    <th>Total Orders</th>
                    <td>{{ $invoice->order_count ?? 0 }}</td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>{{ number_format($invoice->total ?? 0, 2) }} Kyats</td>
                </tr>
            </table>

            <p class="highlight">Thank you for your trust and orders!</p>
        </div>
        <div class="invoice-footer">
            &copy; {{ date('Y') }} Catering Management System. All rights reserved.
        </div>
    </div>
</body>

</html>