@extends('layouts.app')

@section('title', 'Feedback Details')

@section('content')
<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #264653;">
            Feedbacks from {{ $employee->name }}
            <span class="fw-normal text-muted" style="font-size: 1rem;">
                (ID: {{ $employee->emp_id }})
            </span>
        </h4>
        <a href="{{ route('feedback') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i> Back 
        </a>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('feedback.detail', $employee->emp_id) }}" class="card shadow-sm p-4 mb-4 border-start border-5" style="border-color: #2A9D8F;">
        <h5 class="fw-bold mb-3" style="color: #264653;">Feedback Filter</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label fw-semibold text-muted">Month</label>
                <select name="month" id="month" class="form-select border-0 shadow-sm" style="background-color: #F8F9FA;">
                    <option value="" selected disabled>-- All Months --</option>
                    @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label for="year" class="form-label fw-semibold text-muted">Year</label>
                <select name="year" id="year" class="form-select border-0 shadow-sm" style="background-color: #F8F9FA;">
                    <option value="" disabled selected>-- All Years --</option>
                    @foreach($availableYears as $year)
                    <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                    <i class="fas fa-filter me-1"></i> Apply Filter
                </button>
                <a href="{{ route('feedback.detail', $employee->emp_id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-sync-alt me-1"></i> Reset
                </a>
            </div>
        </div>
    </form>

    <!-- Feedback Table -->
    <div class="container py-5">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-4 fw-bold" style="color: #264653; border-bottom: 2px solid #2A9D8F; padding-bottom: 10px;">
                    Feedback Details for {{ $employee->name }} ({{ $employee->emp_id }})
                </h5>

                @if($feedbackList->isEmpty())
                <div class="alert alert-warning text-center fw-semibold" style="background-color: #fff3cd;">
                    No feedback records found.
                </div>
                @else
                <div class="table-responsive rounded shadow-sm">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead style="background-color: #E9C46A;" class="text-center text-dark">
                            <tr>
                                <th class="text-start">Message</th>
                                <th>Rating</th>
                                <th>Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($feedbackList as $feedback)
                            <tr class="text-center">
                                <td class="text-start" style="white-space: pre-wrap;">{{ $feedback->text }}</td>
                                <td>
                                    @php
                                    $fullStars = floor($feedback->rating); // Whole stars
                                    $halfStar = ($feedback->rating - $fullStars) >= 0.5; // Half star
                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0); // Remaining stars
                                    @endphp

                                    @for ($i = 0; $i < $fullStars; $i++)
                                        <i class="fas fa-star text-warning"></i>
                                        @endfor

                                        @if ($halfStar)
                                        <i class="fas fa-star-half-alt text-warning"></i>
                                        @endif

                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            <i class="far fa-star text-muted"></i>
                                            @endfor
                                </td>

                                <td>{{ \Carbon\Carbon::parse($feedback->updated_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Custom Pagination -->
                @if ($feedbackList->hasPages())
                <div class="d-flex justify-content-end mt-4">
                    <nav>
                        <ul class="pagination pagination-sm mb-0">
                            {{-- Previous Page --}}
                            @if ($feedbackList->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                            </li>
                            @else
                            <li class="page-item">
                                <a class="page-link rounded-pill border-0 text-white" href="{{ $feedbackList->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                            </li>
                            @endif

                            {{-- Page Numbers --}}
                            @foreach ($feedbackList->links()->elements[0] as $page => $url)
                            @if ($page == $feedbackList->currentPage())
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
                            @if ($feedbackList->hasMorePages())
                            <li class="page-item">
                                <a class="page-link rounded-pill border-0 text-white" href="{{ $feedbackList->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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
        </div>
    </div>

</div>
@endsection