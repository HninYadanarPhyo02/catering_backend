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
   <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f1;">
    <form method="GET" action="{{ route('feedback.detail', $employee->emp_id) }}" class="d-flex flex-wrap align-items-center gap-2">

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
            <button type="submit" class="btn shadow-sm text-white rounded-pill" style="background-color: #FFA726;">
                <i class="fas fa-filter me-1"></i> Apply
            </button>

            @if(request('month') || request('year'))
            <a href="{{ route('feedback.detail', $employee->emp_id) }}" class="btn btn-outline-dark shadow-sm rounded-pill">
                <i class="fas fa-sync-alt me-1"></i> Clear
            </a>
            @endif
        </div>

    </form>
</div>

    <!-- Feedback Table -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header" style="background-color: #FFE0B2; border-bottom: 1px solid #dee2e6;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">
                    <i class="bi bi-chat-left-dots-fill me-2 text-warning"></i> Feedback Details
                </h5>
                <span class="badge bg-warning text-dark">{{ $feedbackList->total() }} Feedbacks</span>
            </div>
        </div>

        @if($feedbackList->isEmpty())
        <div class="text-center py-4" style="background-color: #FFF3E0;">
            <i class="fas fa-inbox fa-2x text-warning mb-2"></i>
            <p class="mb-0 fw-semibold text-muted">No feedback records found.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-center text-uppercase" style="background-color: #FFF3E0;">
                    <tr>
                        <th class="text-start">Message</th>
                        <th>Rating</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @foreach($feedbackList as $feedback)
                    <tr>
                        <td class="text-start" style="white-space: pre-wrap;">{{ $feedback->text }}</td>
                        <td>
                            @php
                            $fullStars = floor($feedback->rating);
                            $halfStar = ($feedback->rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
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

        <!-- Pagination -->
        @if ($feedbackList->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $feedbackList->links('vendor.pagination.bootstrap-5') }}
        </div>
        @endif
        @endif
    </div>

</div>
@endsection
