@extends('layouts.app')

@section('title', 'Available Menus')

@section('content')
<div class="container-fluid px-3 mt-4">

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Order Creation Form -->
    <div class="d-flex justify-content-center">
    <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 700px; width: 100%; margin: auto; background-color: #fff3e6;">
    <form action="{{ route('orders.store') }}" method="POST" novalidate id="createOrderForm">
        @csrf
        <h5 class="fw-bold mb-3" style="color: #e76f51;">
            <i class="fas fa-utensils me-2"></i> Create New Order
        </h5>

        <!-- Unit Price -->
        <div class="mb-3">
            <label for="price" class="form-label fw-semibold">💰 Unit Price (Kyat)</label>
            <input type="number" name="price" id="price" class="form-control shadow-sm rounded"
                   required min="0" step="1"
                   oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                   placeholder="e.g. 5000">
        </div>

        <!-- Order Items -->
        <div class="mb-3">
            <label class="form-label fw-semibold">🍱 Order Items</label>
            <div id="items-container">
                <div class="item-row mb-3 p-3 border rounded shadow-sm bg-light">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <select name="items[0][food_id]" class="form-select shadow-sm rounded" required>
                                <option value="" disabled selected>Select a food item...</option>
                                @foreach ($foods as $food)
                                    <option value="{{ $food->food_id }}">{{ $food->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="date" name="items[0][date]" class="form-control shadow-sm rounded" required>
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-item">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" id="add-item" class="btn btn-outline-success mt-2 shadow-sm">
                <i class="fas fa-plus-circle me-1"></i> Add Another Item
            </button>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn text-white px-4 shadow-sm" style="background-color: #f4a261;">
                <i class="fas fa-cart-plus me-1"></i> Submit Order
            </button>
        </div>
    </form>
</div>

    </div>
</div>


    <!-- Orders List -->
     <div class="container-fluid px-4 mt-4">
    <div class="card shadow-sm rounded">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0 fw-bold text-secondary">Orders</h5>
        </div>

        <div class="card-body">
            <div class="row g-2 align-items-center mb-4 ">
                <div class="col-md-4">
                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        value="{{ request('search') }}"
                        class="form-control shadow-sm"
                        placeholder="Search by food, date, or price">
                </div>
                <div class="col-md-auto">
                    @if(request('search'))
                    <a href="{{ url('/orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times-circle me-1"></i> Clear
                    </a>
                    @endif
                </div>
            </div>

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
                    <tbody id="ordersTableBody">
                        @forelse ($orders->sortBy('date') as $order)
                        <tr class="text-center">
                            <td class="fw-semibold text-start">{{ $order->food_name }}</td>
                            <td>{{ number_format($order->price) }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                            <td>
                                <!-- Edit Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-edit-order text-white px-3"
                                        data-id="{{ $order->id }}"
                                        data-food_id="{{ $order->food_id }}"
                                        data-date="{{ $order->date }}"
                                        data-price="{{ $order->price }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editOrderModal"
                                        style="background-color: #E6A503"
                                        >
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Delete -->
                                <form action="{{ route('orders.destroyByDate', ['date' => \Carbon\Carbon::parse($order->date)->format('Y-m-d')]) }}"
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete all orders on {{$order->date}} ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
        class="btn btn-sm text-white px-3"
        style="background-color: #E63946;"
        title="Delete">
    <i class="fas fa-trash me-1"></i>
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
    </div></div>

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

<!-- Edit Order Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1" aria-labelledby="editOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #e76f51;">
                <h5 class="modal-title" id="editOrderModalLabel"><i class="fas fa-edit me-2"></i> Edit Order</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editOrderForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Food</label>
                        <select name="food_id" id="edit_food_id" class="form-select" required>
                            <option value="" disabled>Select Food</option>
                            @foreach($foods as $food)
                            <option value="{{ $food->food_id }}">{{ $food->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price (Kyat)</label>
                        <input type="text" name="price" id="edit_price" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order Date</label>
                        <input type="date" name="date" id="edit_date" class="form-control bg-light" readonly>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn text-white" style="background-color: #2A9D8F;">
                        <i class="fas fa-save me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<style>
    button.btn:hover {
    filter: brightness(85%);
    color: white !important;
}

</style>
<!-- JavaScript -->
<script>
    let itemIndex = 1;

    // Add new item row dynamically
    document.getElementById('add-item').addEventListener('click', function () {
        const container = document.getElementById('items-container');
        const row = document.createElement('div');
        row.classList.add('item-row', 'mb-3', 'p-3', 'border', 'rounded', 'shadow-sm');
        row.style.backgroundColor = '#f8f9fa';
        row.innerHTML = `
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <select name="items[${itemIndex}][food_id]" class="form-select" required>
                        <option value="" disabled selected>Select a food item...</option>
                        @foreach ($foods as $food)
                            <option value="{{ $food->food_id }}">{{ $food->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
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

    // Remove item row (delegated)
    document.getElementById('items-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-item')) {
            e.target.closest('.item-row').remove();
        }
    });

    // Populate Edit Modal fields when clicking edit buttons
    document.querySelectorAll('.btn-edit-order').forEach(button => {
        button.addEventListener('click', function () {
            const orderId = this.dataset.id;
            const foodId = this.dataset.food_id;
            const date = this.dataset.date;
            const price = this.dataset.price;

            document.getElementById('edit_food_id').value = foodId;
            document.getElementById('edit_date').value = date;
            document.getElementById('edit_price').value = price;

            document.getElementById('editOrderForm').action = `/orders/${orderId}`;
        });
    });

    // Optional: Live search (remove if you want classic form submit search)
    document.getElementById('searchInput').addEventListener('input', function () {
        const search = this.value;

        fetch(`{{ url('/orders') }}?search=${encodeURIComponent(search)}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('ordersTableBody').innerHTML = data.html;
            // Re-attach edit modal click handlers after content replacement
            reattachEditButtons();
        })
        .catch(error => {
            console.error('Error fetching orders:', error);
        });
    });

    function reattachEditButtons() {
        document.querySelectorAll('.btn-edit-order').forEach(button => {
            button.removeEventListener('click', () => {}); // Remove any previous listener
            button.addEventListener('click', function () {
                const orderId = this.dataset.id;
                const foodId = this.dataset.food_id;
                const date = this.dataset.date;
                const price = this.dataset.price;

                document.getElementById('edit_food_id').value = foodId;
                document.getElementById('edit_date').value = date;
                document.getElementById('edit_price').value = price;

                document.getElementById('editOrderForm').action = `/orders/${orderId}`;
            });
        });
    }
</script>
@endsection
