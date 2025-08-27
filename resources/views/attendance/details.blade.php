@extends('layouts.app')

@section('title', 'Attendance Details')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #264653;">
            Attendance Records for {{ $employee->name }}
            <span class="fw-normal text-muted" style="font-size: 1rem;">(ID: {{ $employee->emp_id }})</span>
        </h4>
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Back
        </a>
    </div>

    <!-- Filter Form -->
        <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f1;">
        <form method="GET" action="{{ route('attendance.details', $employee->emp_id) }}" class="d-flex flex-wrap align-items-center gap-2">

            <!-- Month -->
            <div class="flex-grow-1 flex-md-auto" style="min-width: 140px;">
                <label for="month" class="form-label fw-semibold mb-1">Month</label>
                <select name="month" id="month" class="form-select shadow-sm rounded">
                    <option value="" selected disabled>-- All Months --</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Year -->
            <div class="flex-grow-1 flex-md-auto" style="min-width: 140px;">
                <label for="year" class="form-label fw-semibold mb-1">Year</label>
                <select name="year" id="year" class="form-select shadow-sm rounded">
                    <option value="" disabled selected>-- All Years --</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-2 flex-wrap">
                <button type="submit" class="btn shadow-sm text-white" style="background-color: #FFA726;">
                    <i class="fas fa-filter me-1"></i> Apply
                </button>

                @if(request('month') || request('year'))
                <a href="{{ route('attendance.details', $employee->emp_id) }}" class="btn btn-outline-dark shadow-sm rounded-pill">
                    <i class="fas fa-sync-alt me-1"></i> Clear
                </a>
                @endif
            </div>

        </form>
    </div>


    <!-- Attendance Table -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header" style="background-color: #E0F7FA; border-bottom: 1px solid #dee2e6;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-calendar-check-fill me-2 text-info"></i> Attendance Details
                </h5>
                <span class="badge bg-info text-dark">{{ $attendanceRecords->total() }} Records</span>
            </div>
        </div>

        @if($attendanceRecords->isEmpty())
        <div class="text-center py-4" style="background-color: #E0F7FA;">
            <i class="fas fa-inbox fa-2x text-info mb-2"></i>
            <p class="mb-0 fw-semibold text-muted">No attendance records found.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-center text-uppercase" style="background-color: #B2EBF2;">
                    <tr>
                        <th>Employee ID</th>
                        <th class="text-start">Employee Name</th>
                        <th>Menu</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Check-Out</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($attendanceRecords as $record)
                    <tr>
                        <td>{{ $record->emp_id }}</td>
                        <td class="text-start">{{ $employee->name }}</td>
                        <td>{{ $record->foodmonthpriceByDate->food_name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($record->date)->format('Y-m-d') }}</td>
                        <td>
                            @if($record->status === 'present')
                                <span class="badge text-white" style="background-color: #2A9D8F;">Present</span>
                            @elseif($record->status === 'absent')
                                <span class="badge text-white" style="background-color: #E76F51;">Absent</span>
                            @else
                                <span class="badge bg-secondary">{{ $record->status }}</span>
                            @endif
                        </td>
                        <td>{{ $record->check_out ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($attendanceRecords->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $attendanceRecords->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
