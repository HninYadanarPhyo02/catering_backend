@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-primary fw-bold text-center">🧾 Order Details</h3>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-info text-white fw-semibold">
                    Order #{{ $order->id }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Employee ID:</label>
                        <p class="fs-6">{{ $order->emp_id }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Employee Name:</label>
                        <p class="fs-6">{{ $order->employee->name ?? 'N/A' }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-bold">Order Date:</label>
                        <p class="fs-6">{{ \Carbon\Carbon::parse($order->date)->format('F j, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary fw-bold">Menu:</label>
                        <ul class="list-group list-group-flush">
                            @forelse($order->foodMonthPricesByDate as $menu)
                                <li class="list-group-item">{{ $menu->food_name }}</li>
                            @empty
                                <li class="list-group-item text-muted">No menu items found.</li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="text-end">
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary px-4">
                            ← Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
