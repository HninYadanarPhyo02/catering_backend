@extends('layouts.app')

@section('title', 'User Invoices')

@section('content')
<div class="container-fluid px-3 mt-4">

  {{-- Filter Form --}}
  <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 700px; width: 100%; margin: auto; background-color: #fff3e6;">
    <form method="GET" action="{{ route('invoices.index') }}" class="d-flex flex-wrap gap-3 align-items-end">
        <!-- Header -->
        <div class="w-100 mb-2">
            <h5 class="fw-bold mb-0" style="color: #e76f51; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                <i class="bi bi-receipt me-2"></i> Filter Invoices
            </h5>
        </div>

        <!-- Employee Select -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="emp_id" class="form-label fw-semibold mb-1">Employee</label>
            <select name="emp_id" id="emp_id" class="form-select shadow-sm rounded">
                <option value="" selected>All Employees</option>
                @foreach($employees as $employee)
                @if(strtolower($employee->role) !== 'admin')
                <option value="{{ $employee->emp_id }}" {{ request('emp_id') == $employee->emp_id ? 'selected' : '' }}>
                    {{ $employee->emp_id }} - {{ $employee->name }}
                </option>
                @endif
                @endforeach
            </select>
        </div>

        <!-- Month Select -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 140px;">
            <label for="month" class="form-label fw-semibold mb-1">Month</label>
            <select name="month" id="month" class="form-select shadow-sm rounded">
                <option value="" selected>All Months</option>
                @foreach($months as $monthNum)
                @php
                $monthName = \Carbon\Carbon::create()->month($monthNum)->format('F');
                @endphp
                <option value="{{ $monthNum }}" {{ request('month') == $monthNum ? 'selected' : '' }}>
                    {{ $monthName }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Year Select -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 120px;">
            <label for="year" class="form-label fw-semibold mb-1">Year</label>
            <select name="year" id="year" class="form-select shadow-sm rounded">
                <option value="" selected>All Years</option>
                @foreach($years as $year)
                <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                    {{ $year }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2 flex-wrap ms-auto">
            <button type="submit" class="btn text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f4a261;">
                <i class="fas fa-filter"></i> Apply
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-dark shadow-sm d-flex align-items-center gap-1">
                <i class="fas fa-sync-alt"></i> Reset
            </a>
        </div>
    </form>
</div>


  {{-- Show message if exists --}}
  @if(isset($message))
  <div class="alert alert-info">{{ $message }}</div>
  @endif

  {{-- Invoice Records --}}
  @if(isset($invoices) && $invoices->count())

  {{-- Flexible Send All Button --}}
  <div class="d-flex justify-content-end mb-3">
    <form action="{{ route('invoices.send-all') }}" method="POST" onsubmit="return confirm('Are you sure you want to send invoices to all employees who ordered this month?')">
      @csrf
      <button type="submit" class="btn btn-sm"
        style="color: #2A9D8F; border: 1px solid #2A9D8F; background-color: transparent;"
        onmouseover="this.style.backgroundColor='#2A9D8F'; this.style.color='white';"
        onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2A9D8F';">
        <i class="fas fa-paper-plane me-1"></i> Send All Invoices
      </button>


    </form>
  </div>

  @foreach($invoices as $invoice)
  <div class="container-fluid px-4 mt-1">
    <div class="card-header bg-light fw-bold">
      {{ $invoice['emp_id'] }} - {{ $invoice['emp_name'] }} ({{ $invoice['emp_email'] }})
    </div>
    <div class="card-body">
      <table class="table table-bordered align-middle">
        <thead class="table-secondary">
          <tr>
            <th>Date</th>
            <th>Food Name</th>
            <th>Price</th>
            <th>Status</th>
            <th>Check Out</th>
          </tr>
        </thead>
        <tbody>
          @foreach($invoice['attendances'] as $record)
          <tr>
            <td>{{ $record['date'] }}</td>
            <td>{{ $record['food_name'] }}</td>
            <td>{{ number_format($record['price'], 2) }}</td>
            <td>{{ ucfirst($record['status']) }}</td>
            <td>{{ $record['check_out'] ?? '-' }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot class="table-light">
          <tr>
            <td colspan="4" class="text-end fw-bold">Total Amount:</td>
            <td class="fw-bold text-success">{{ number_format($invoice['total_amount'], 2) }} Kyats</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
  @endforeach

  {{-- Pagination --}}
  <div class="d-flex justify-content-center">
    {{ $invoices->withQueryString()->links() }}
  </div>
  @else
  <p class="text-muted"></p>
  @endif

</div>
@endsection