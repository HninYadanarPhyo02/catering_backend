@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="container mt-5">
    <!-- Search Form -->
    <div class="card shadow-sm border-0 mb-5 rounded">
  <div class="card-body">
    <h5 class="mb-3 fw-semibold text-primary border-bottom pb-2" style="border-color: #0d6efd;">
      Employee Attendance
    </h5>

    <form action="{{ url('/attendance') }}" method="GET" class="row g-3 align-items-end">
      <div class="col-md-6">
        <input
          type="text"
          id="searchInput"
          name="search"
          value="{{ request('search') }}"
          class="form-control rounded shadow-sm"
          placeholder="Enter employee ID or name"
        />
      </div>

      <div class="col-md-auto d-grid">
        <button type="submit" class="btn btn-primary shadow-sm">
          <i class="bi bi-search me-1"></i> Search
        </button>
      </div>

      @if(request('search'))
      <div class="col-md-auto d-grid">
        <a href="{{ url('/attendance') }}" class="btn btn-outline-secondary shadow-sm">
          <i class="bi bi-x-circle me-1"></i> Clear
        </a>
      </div>
      @endif
    </form>
  </div>
</div>


    <!-- Attendance Table -->
    @if($attendanceList->isEmpty())
    <div class="alert alert-warning text-center" role="alert">
        No attendance records found.
    </div>
    @else
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light text-center">
                <tr class="text-secondary">
                    <th>ID</th>
                    <th>Employee ID</th>
                    <th class="text-start">Employee Name</th>
                    <th>Menu</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Check-Out</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceList as $record)
                <tr class="text-center align-middle">
                    <td class="fw-semibold">{{ $record->id }}</td>
                    <td>{{ $record->emp_id }}</td>
                    <td class="text-start">{{ $record->employee->name }}</td>
                    <td>{{ $record->foodmonthpriceByDate->food_name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                    <td>
                        @if($record->status === 'Present')
                        <span class="badge bg-success">Present</span>
                        @elseif($record->status === 'Absent')
                        <span class="badge bg-danger">Absent</span>
                        @else
                        <span class="badge bg-secondary">{{ $record->status }}</span>
                        @endif
                    </td>
                    <td>{{ $record['check_out'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@endsection