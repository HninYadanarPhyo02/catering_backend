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
    <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f1;">
    <form method="GET" action="{{ route('registered-orders.details', $employee->emp_id) }}" class="d-flex flex-wrap align-items-center gap-2">

        <!-- Order Date -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="date" class="form-label fw-semibold mb-1">Order Date</label>
            <input type="date" name="date" id="date" class="form-control shadow-sm rounded" value="{{ request('date') }}">
        </div>

        <!-- Menu -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="menu" class="form-label fw-semibold mb-1">Menu</label>
            <select name="menu" id="menu" class="form-select shadow-sm rounded">
                <option value="">All Menus</option>
                @foreach($menus as $menu)
                    <option value="{{ $menu }}" {{ request('menu') == $menu ? 'selected' : '' }}>
                        {{ $menu }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn shadow-sm text-white" style="background-color: #FFA726;">
                <i class="fas fa-filter me-1"></i> Apply
            </button>

            @if(request('date') || request('menu'))
            <a href="{{ route('registered-orders.details', $employee->emp_id) }}" class="btn btn-outline-dark shadow-sm">
                <i class="fas fa-sync-alt me-1"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>


    @if($orders->isEmpty())
    <div class="alert alert-warning text-center fw-semibold">
        No orders found.
    </div>
    @else
    <div class="table-responsive shadow-sm rounded mb-4">
        <div class="table-responsive">
    <table class="table table-borderless align-middle mb-0">
        <thead class="border-bottom text-center text-secondary">
            <tr>
                <th class="fw-semibold">Order ID</th>
                <th class="fw-semibold" style="width: 180px;">Date</th>
                <th class="fw-semibold text-start">Menu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr class="text-center align-middle py-2 border-bottom">
                <td class="text-primary fw-bold">{{ $order->id }}</td>
                <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                <td class="text-start">
                    @forelse($order->foodMonthPricesByDate as $menu)
                    <span class="d-inline-block bg-light text-success px-2 py-1 rounded me-1 mb-1">
                        <i class="fas fa-utensils me-1"></i>{{ $menu->food_name }}
                    </span>
                    @empty
                    <span class="text-muted fst-italic">No menu</span>
                    @endforelse
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

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