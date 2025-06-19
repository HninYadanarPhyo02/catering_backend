@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold mb-0">🧾 Invoices</h2>
        <a href="{{ route('invoices.create') }}" class="btn btn-success px-4">
            <i class="bi bi-plus-circle me-1"></i> Add Invoice
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">Id</th>
                        <th>Customer</th>
                        <th>Event</th>
                        <th>Invoice Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Status</th>
                        <th style="width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                    <tr>
                        <td class="fw-bold text-muted">{{ $invoice->id }}</td>
                        <td>{{ $invoice->customer_name }}</td>
                        <td>{{ $invoice->event_name ?? '-' }}</td>
                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                        <td class="text-success">${{ number_format($invoice->total_amount, 2) }}</td>
                        <td class="text-primary">${{ number_format($invoice->paid_amount, 2) }}</td>
                        <td>
                            @if($invoice->status === 'paid')
                            <span class="badge bg-success">Paid</span>
                            @elseif($invoice->status === 'partial')
                            <span class="badge bg-warning text-dark">Partial</span>
                            @else
                            <span class="badge bg-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete invoice?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No invoices found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>
@endsection