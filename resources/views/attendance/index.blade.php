@extends('layouts.app')

@section('title', 'Attendance')

@section('content')
<div class="container-fluid px-3 mt-4">
        <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f1;">
    <!-- Filter Form -->
    <form method="GET" action="{{ route('attendance.index') }}" class="d-flex flex-wrap align-items-center gap-3 p-3 shadow-sm rounded-4" style="background-color: #fff3e6;">

    <!-- User Dropdown -->
    <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
        <label for="emp_id" class="form-label fw-semibold mb-1">User</label>
        <select name="emp_id" id="emp_id" class="form-select shadow-sm rounded">
            <option value="" selected>All Users</option>
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
    <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn shadow-sm text-white" style="background-color: #FFA726;">
            <i class="fas fa-filter me-1"></i> Apply
        </button>

        @if(request('emp_id'))
        <a href="{{ route('attendance.index') }}" class="btn btn-outline-dark shadow-sm rounded-pill">
            <i class="fas fa-sync-alt me-1"></i> Clear
        </a>
        @endif
    </div>
</form>

        </div>
        <div class="container-fluid px-3 mt-4">
    <!-- Attendance Summary -->
    @if($attendanceSummary->isEmpty())
    <div class="alert alert-warning text-center shadow-sm rounded-3">
        <i class="bi bi-exclamation-triangle me-2"></i> No attendance records found.
    </div>
    @else
    <div class="card shadow-sm border-0 rounded-3">
    <div class="card-header" style="background-color: #B2EBF2; border-bottom: 1px solid #dee2e6;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-people-fill me-2 text-info"></i> Attendance Summary
            </h5>
            <span class="badge bg-info text-dark">{{ $attendanceSummary->count() }} Users</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #E0F7FA;" class="text-center text-uppercase">
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Total Records</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($attendanceSummary as $summary)
                <tr>
                    <td class="fw-semibold text-dark">{{ $summary->emp_id }}</td>
                    <td class="text-dark">{{ $summary->employee->name ?? '-' }}</td>
                    <td class="fw-semibold text-dark">{{ $summary->record_count }}</td>
                    <td>
                        <a href="{{ route('attendance.details', $summary->emp_id) }}" 
                           class="btn btn-sm text-white px-3" 
                           style="background-color: #FFA726;">
                            <i class="far fa-eye me-1"></i> 
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
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