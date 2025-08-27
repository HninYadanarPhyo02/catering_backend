@extends('layouts.app')

@section('title', 'Invoice Details')

@section('content')
<div class="container mt-5">

    {{-- Invoice Card --}}
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4 p-md-5">

            {{-- Header --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-primary">Invoice Details</h4>
                <span class="text-muted">Invoice ID: {{ $invoice->invoice_id }}</span>
            </div>

            {{-- Employee & Month Info --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Employee:</strong> {{ $invoice->employee->name ?? 'N/A' }} ({{ $invoice->emp_id }})</p>
                    <p class="mb-1"><strong>Month:</strong> {{ DateTime::createFromFormat('!m', $invoice->month)->format('F') }} {{ $invoice->year }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Total Amount:</strong> {{ number_format($invoice->total_amount, 2) }} Kyats</p>
                    <p class="mb-1"><strong>Invoice Date:</strong> {{ now()->format('Y-m-d') }}</p>
                </div>
            </div>

            {{-- Invoice Items Table --}}
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary text-white">
                        <tr>
                            <th>Date</th>
                            <th>Food Item</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Checked Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->details as $detail)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($detail->date)->format('d M Y') }}</td>
                            <td>{{ $detail->food_name }}</td>
                            <td>{{ number_format($detail->price, 2) }} Kyats</td>
                            <td>
                                <span class="badge {{ strtolower($detail->status) === 'present' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($detail->status) }}
                                </span>
                            </td>
                            <td>
                                @if($detail->check_out)
                                <i class="fas fa-check text-success"></i>
                                @else
                                <i class="fas fa-times text-danger"></i>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Back Button --}}
            <div class="mt-4 text-end">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Optional Styling --}}
<style>
.table-primary {
    background-color: #2a9d8f;
}
.table-hover tbody tr:hover {
    background-color: rgba(42, 157, 143, 0.1);
}
.badge {
    font-size: 0.85rem;
    padding: 0.4em 0.6em;
}
</style>
@endsection
