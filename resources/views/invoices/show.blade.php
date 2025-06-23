@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold" style="color: #2A9D8F;">Invoice Details</h4>
                <span class="text-muted">Invoice ID: {{ $invoice->invoice_id }}</span>
            </div>

            {{-- Invoice Info --}}
            <div class="mb-4">
                <p><strong>Employee:</strong> {{ $invoice->employee->name ?? 'N/A' }} ({{ $invoice->emp_id }})</p>
                <p><strong>Month:</strong> {{ DateTime::createFromFormat('!m', $invoice->month)->format('F') }} {{ $invoice->year }}</p>
                <p><strong>Total Amount:</strong> {{ number_format($invoice->total_amount, 2) }} Kyats</p>
                <p><strong>Invoice Date:</strong> {{ now()->format('Y-m-d') }}</p>

            </div>

            {{-- Invoice Items --}}
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
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
                            <td>${{ number_format($detail->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ strtolower($detail->status) === 'present' ? 'success' : 'secondary' }}">
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

            <div class="mt-4 text-end">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection