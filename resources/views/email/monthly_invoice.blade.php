<!-- resources/views/emails/monthly_invoice.blade.php -->

<h2>Invoice ID: {{ $invoice->invoice_id }}</h2>
<p>Employee ID: {{ $invoice->emp_id }}</p>
<p>Month/Year: {{ $invoice->month }}/{{ $invoice->year }}</p>
<p>Total Amount: {{ number_format($invoice->total_amount, 2) }}</p>

<h4>Details:</h4>
<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>Date</th>
            <th>Food</th>
            <th>Price</th>
            <th>Status</th>
            <th>Check Out</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $item)
            <tr>
                <td>{{ $item->date }}</td>
                <td>{{ $item->food_name }}</td>
                <td>{{ number_format($item->price, 2) }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->check_out }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
