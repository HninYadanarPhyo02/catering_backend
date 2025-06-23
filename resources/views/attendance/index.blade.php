@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('content')
<div class="container py-5">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('attendance.index') }}" class="mb-5 p-4 border-0 rounded-4 shadow-sm bg-white">
        <h4 class="fw-bold mb-4 pb-2 border-bottom" style="color: #2A9D8F; border-color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <i class="bi bi-calendar-check me-2"></i> Employee Attendance
        </h4>

        <div class="row g-3 align-items-end">
            <!-- Employee Dropdown -->
            <div class="col-md-6 col-lg-4">
                <label for="emp_id" class="form-label fw-semibold text-muted">Select Employee</label>
                <select name="emp_id" id="emp_id" class="form-select shadow-sm rounded">
                    <option value="" disabled selected>All Employees</option>
                    @foreach($employees as $employee)
                    @if(strtolower($employee->role) !== 'admin')
                    <option value="{{ $employee->emp_id }}" {{ request('emp_id') == $employee->emp_id ? 'selected' : '' }}>
                        {{ $employee->emp_id }} - {{ $employee->name }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>


            <!-- Filter and Reset Buttons -->
            <div class="col-md-6 d-flex flex-wrap gap-2 mt-2">
                <button type="submit"
                    class="btn px-4 py-2 rounded-3 shadow-sm text-white"
                    style="background-color: #2A9D8F; border: none;">
                    <i class="fas fa-filter me-1"></i> Apply Filter

                </button>

                <a href="{{ route('attendance.index') }}"
                    class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
                    <i class="fas fa-sync-alt"></i> Reset

                </a>
            </div>
        </div>
    </form>

    <!-- Attendance Summary -->
    @if($attendanceSummary->isEmpty())
    <div class="alert alert-warning text-center shadow-sm rounded-3">
        <i class="bi bi-exclamation-triangle me-2"></i> No attendance records found.
    </div>
    @else
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-bottom fw-semibold text-secondary">
            <i class="bi bi-people-fill me-2 text-primary"></i> Attendance Summary
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center text-uppercase">
                    <tr>
                        <th>Employee ID</th>
                        <th>Employee Name</th>
                        <th>Total Records</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendanceSummary as $summary)
                    <tr class="text-center">
                        <td class="fw-semibold text-secondary">{{ $summary->emp_id }}</td>
                        <td>{{ $summary->employee->name }}</td>
                        <td class="text-success fw-bold">{{ $summary->record_count }}</td>
                        <td>
                            <a href="{{ route('attendance.details', $summary->emp_id) }}"
                                class="btn btn-sm text-white px-3"
                                style="background-color: #2A9D8F;">
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
    @if ($attendanceSummary->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($attendanceSummary->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $attendanceSummary->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($attendanceSummary->links()->elements[0] as $page => $url)
                @if ($page == $attendanceSummary->currentPage())
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
                @if ($attendanceSummary->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $attendanceSummary->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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