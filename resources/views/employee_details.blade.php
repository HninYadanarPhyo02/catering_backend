@extends('layouts.app')

@section('title', 'Employee Orders')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #264653;">
            Orders for {{ $employee->name }}
            <span class="fw-normal text-muted" style="font-size: 1rem;">(ID: {{ $employee->id }})</span>
        </h4>
        <a href="{{ route('registeredorder') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>

    <!-- Always show filter form -->
    <form method="GET" action="{{ route('registered-orders.details', $employee->emp_id) }}"
        class="card shadow-sm p-4 mb-4 border-start border-5" style="border-color: #2A9D8F;">
        <h5 class="fw-bold mb-3" style="color: #264653;">Order Filter</h5>
        <div class="row g-3">
            <!-- Order Date -->
            <div class="col-md-3">
                <label for="date" class="form-label fw-semibold text-muted">Order Date</label>
                <input type="date"
                    name="date"
                    id="date"
                    class="form-control border-0 shadow-sm"
                    style="background-color: #F8F9FA;"
                    value="{{ request('date') }}">
            </div>

            <!-- Menu -->
            <div class="col-md-3">
                <label for="menu" class="form-label fw-semibold text-muted">Menu</label>
                <select name="menu"
                    id="menu"
                    class="form-select border-0 shadow-sm"
                    style="background-color: #F8F9FA;">
                    <option value="">All Menus</option>
                    @foreach($menus as $menu)
                    <option value="{{ $menu }}" {{ request('menu') == $menu ? 'selected' : '' }}>
                        {{ $menu }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn text-white" style="background-color: #2A9D8F;">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ route('registered-orders.details', $employee->emp_id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>

    @if($orders->isEmpty())
    <div class="alert alert-warning text-center fw-semibold">
        No orders found.
    </div>
    @else
    <div class="table-responsive shadow-sm rounded mb-4">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light text-center">
                <tr class="text-secondary">
                    <th style="width: 200px;">Order ID</th>
                    <th style="width: 150px;">Date</th>
                    <th>Menu</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="text-center">
                    <td class="fw-semibold text-primary">{{ $order->id }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                    <td class="text-start">
                        @forelse($order->foodMonthPricesByDate as $menu)
                        <div class="mb-1">
                            <i class="fas fa-utensils text-success me-1"></i>
                            {{ $menu->food_name }}
                        </div>
                        @empty
                        <span class="text-muted">No menu</span>
                        @endforelse
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Styled Pagination --}}
    @if ($orders->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page Link --}}
                @if ($orders->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $orders->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Pagination Elements --}}
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

                {{-- Next Page Link --}}
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

    @endif

</div>
@endsection