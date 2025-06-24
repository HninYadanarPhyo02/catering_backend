@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="container mt-5">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Order Creation Form -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header" style="background-color: #264653; color: white;">
            <h5 class="mb-0">Create New Order</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST" novalidate>
                @csrf

                <div class="row g-4 align-items-center">
                    <!-- Total Price -->
                    <div class="col-md-6">
                        <label for="price" class="form-label fw-semibold">Unit Price (Kyat)</label>
                        <input type="number" name="price" id="price" class="form-control" placeholder="e.g. 5000"
                            required min="0" step="1"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">

                    </div>
                </div>

                <!-- Order Items -->
                <div class="mt-4 mb-3">
                    <label class="form-label fw-semibold text-muted">Order Items</label>
                    <div id="items-container">
                        <div class="item-row mb-3 p-3 border rounded shadow-sm" style="background-color: #f8f9fa;">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5">
                                    <select name="items[0][food_id]" class="form-select" required>
                                        <option value="" disabled selected>Select a food item...</option>
                                        @foreach ($foods as $food)
                                        <option value="{{ $food->food_id }}">{{ $food->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="date" name="items[0][date]" class="form-control" required>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger remove-item">X</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-item" class="btn btn-outline-primary mt-2">
                        <i class="fas fa-plus-circle me-1"></i> Add Another Item

                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-end mt-3">
                    <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                        <<i class="fas fa-cart-plus me-1"></i> Submit Order

                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm rounded">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold text-secondary">Orders</h5>
        </div>

        <div class="card-body">
            <!-- Search Form -->
            <form action="{{ url('/orders') }}" method="GET" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4">
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control shadow-sm"
                            placeholder="Search by food, date, or price">
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn text-white" style="background-color: #2A9D8F;">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    @if(request('search'))
                    <div class="col-md-auto">
                        <a href="{{ url('/orders') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times-circle me-1"></i> Clear
                        </a>
                    </div>
                    @endif
                </div>
            </form>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                    <thead class="table-light border-bottom text-center text-secondary">
                        <tr>
                            <th>Food Name</th>
                            <th>Price (Kyat)</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders->sortBy('date') as $order)
                        <tr class="text-center">
                            <td class="fw-semibold text-start">{{ $order->food_name }}</td>
                            <td>{{ number_format($order->price) }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('orders.edit', $order->id) }}"
                                    class="btn btn-sm"
                                    style="color:rgb(230, 165, 3); border: 1px solid rgb(233, 186, 68); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='#E9C46A'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#E9C46A';">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <form action="{{ route('orders.destroyByDate', ['date' => \Carbon\Carbon::parse($order->date)->format('Y-m-d')]) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Are you sure you want to delete all orders for this date?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm"
                                        style="color:rgb(182, 48, 14); border: 1px solid rgb(201, 85, 56); background-color: transparent;"
                                        onmouseover="this.style.backgroundColor='#E76F51'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#E76F51';">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </button>
                                </form>


                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No orders found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <!-- Pagination -->
    @if ($orders->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($orders->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $orders->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($orders->links()->elements[0] as $page => $url)
                @if ($page == $orders->currentPage())
                <li class="page-item active">
                    <span class="page-link rounded-pill border-0 text-white" style="background-color: #264653;">{{ $page }}</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-dark" href="{{ $url }}" style="background-color: #E9F7F6;">{{ $page }}</a>
                </li>
                @endif
                @endforeach

                {{-- Next Page --}}
                @if ($orders->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $orders->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
                </li>
                @else
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&raquo;</span>
                </li>
                @endif
            </ul>
        </nav>
    </div>
    @endif

</div>
<!-- JavaScript to Add/Remove Rows -->
<script>
    let itemIndex = 1;
    document.getElementById('add-item').addEventListener('click', function() {
        const container = document.getElementById('items-container');

        const row = document.createElement('div');
        row.classList.add('item-row', 'mb-3');
        row.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Select Food</label>
                    <select name="items[${itemIndex}][food_id]" class="form-control" required>
                        <option value="">-- Select Food --</option>
                        @foreach ($foods as $food)
                            <option value="{{ $food->food_id }}">{{ $food->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Date</label>
                    <input type="date" name="items[${itemIndex}][date]" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item">X</button>
                </div>
            </div>
        `;
        container.appendChild(row);
        itemIndex++;
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });
</script>
@endsection