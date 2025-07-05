@extends('layouts.app')

@section('title', 'Registered Orders')

@section('content')
<div class="container-fluid px-3 mt-4">
  <!-- Filter Form -->
  <form method="GET" action="{{ route('registeredorder') }}" class="mb-5 p-4 border-0 rounded-4 shadow-sm bg-white">
    <h4 class="fw-bold mb-4 pb-2 border-bottom" style="color: #2A9D8F; border-color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
      <i class="bi bi-receipt me-2"></i> Registered Orders
    </h4>

    <div class="row g-3 align-items-end">
      <!-- Employee Select Dropdown -->
      <div class="col-md-6 col-lg-4">
        <label for="emp_id" class="form-label fw-semibold text-muted">Select User</label>
        <select name="emp_id" id="emp_id" class="form-select shadow-sm rounded">
          <option value="" disabled selected>All Users</option>
          @foreach($employees as $employee)
          @if(strtolower($employee->role) !== 'admin')
          <option value="{{ $employee->emp_id }}" {{ request('emp_id') == $employee->emp_id ? 'selected' : '' }}>
            {{ $employee->emp_id }} - {{ $employee->name }}
          </option>
          @endif
          @endforeach
        </select>
      </div>

      <!-- Buttons -->
      <div class="col-md-6 col-lg-4 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn px-4 text-white d-flex align-items-center gap-2 shadow-sm"
          style="background-color: #2A9D8F;">
         <i class="fas fa-filter me-1"></i> Apply Filter
        </button>
        <a href="{{ route('registeredorder') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
          <i class="fas fa-sync-alt"></i> Reset
        </a>
      </div>
    </div>
  </form>

  @if($orderSummary->isEmpty())
  <div class="alert alert-warning text-center shadow-sm rounded-3">
    <i class="bi bi-exclamation-triangle me-2"></i> No registered orders found.
  </div>
  @else
  <div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom fw-semibold text-secondary">
            <i class="bi bi-people-fill me-2 text-primary"></i> Order Summary
        </div>
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle mb-0">
        <thead class="text-center text-uppercase" style="background-color: #E9F7F6; color: #264653;">
          <tr>
            <th style="width: 20%;">Employee ID</th>
            <th style="width: 30%;">Employee Name</th>
            <th style="width: 25%;">Total Orders</th>
            <th style="width: 25%;">Action</th>
          </tr>
        </thead>
        <tbody>
          @foreach($orderSummary as $order)
          <tr class="text-center">
            <td class="fw-semibold text-muted">{{ $order->emp_id }}</td>
            <td>{{ $order->employee?->name }}</td>
            <td class="fw-bold text-success">{{ $order->order_count }}</td>
            <td>
              <a href="{{ route('registered-orders.details', $order->emp_id) }}"
                class="btn btn-sm text-white px-3"
                style="background-color: #2A9D8F;"
                title="View Details">
                <!-- <i class="fas fa-eye"></i> -->
                <i class="far fa-eye"></i> View Details
              </a>

            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <!-- Pagination -->
  @if ($orderSummary->hasPages())
  <div class="d-flex justify-content-end mt-4">

    <nav>
      <ul class="pagination pagination-sm mb-0">
        {{-- Previous Page Link --}}
        @if ($orderSummary->onFirstPage())
        <li class="page-item disabled">
          <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
        </li>
        @else
        <li class="page-item">
          <a class="page-link rounded-pill border-0 text-white" href="{{ $orderSummary->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
        </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($orderSummary->links()->elements[0] as $page => $url)
        @if ($page == $orderSummary->currentPage())
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
        @if ($orderSummary->hasMorePages())
        <li class="page-item">
          <a class="page-link rounded-pill border-0 text-white" href="{{ $orderSummary->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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