@extends('layouts.app')

@section('title', 'Record Payment')

@section('content')
<div class="container mt-4">
    
    <h1>Record Payment for Invoice #{{ $invoice->id }}</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('payments.store') }}">
        @csrf

        <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">

        <div class="mb-3">
            <label for="payment_date" class="form-label">Payment Date</label>
            <input type="date" id="payment_date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
            @error('payment_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount ($)</label>
            <input type="number" step="0.01" id="amount" name="amount" class="form-control" value="{{ old('amount', $invoice->total) }}" required>
            @error('amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select id="payment_method" name="payment_method" class="form-select">
                <option value="">Select method (optional)</option>
                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
            </select>
            @error('payment_method')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="reference" class="form-label">Reference (optional)</label>
            <input type="text" id="reference" name="reference" class="form-control" value="{{ old('reference') }}">
            @error('reference')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Record Payment</button>
        <a href="{{ route('invoices') }}" class="btn btn-secondary">Back to Invoices</a>
    </form>
</div>
@endsection
