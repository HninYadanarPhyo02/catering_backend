<!-- resources/views/payments/create.blade.php -->

@extends('layouts.app')

@section('title', 'Add Payment')

@section('content')
    <div class="container mt-5">
        <h2>Add Payment for Invoice #{{ $invoice->id }} ({{ $invoice->customer_name }})</h2>

        <form action="{{ route('payments.store', $invoice->id) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="payment_date" class="form-label">Payment Date</label>
                <input type="date" name="payment_date" id="payment_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="amount" class="form-label">Amount</label>
                <input type="number" name="amount" id="amount" step="0.01" max="{{ $invoice->total_amount - $invoice->paid_amount }}" class="form-control" required>
                <small class="text-muted">Remaining balance: ${{ number_format($invoice->total_amount - $invoice->paid_amount, 2) }}</small>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <input type="text" name="payment_method" id="payment_method" class="form-control">
            </div>
            <div class="mb-3">
                <label for="reference" class="form-label">Reference</label>
                <input type="text" name="reference" id="reference" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Payment</button>
            <a href="{{ route('invoices.index') }}" class="btn btn-secondary ms-2">Cancel</a>
        </form>
    </div>
@endsection
