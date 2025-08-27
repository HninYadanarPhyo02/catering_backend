@extends('layouts.app')

@section('title', 'Feedback')

@section('content')
<div class="container-fluid px-3 mt-4">
    <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f1;">
    <form method="GET" action="{{ url('/feedbacks') }}" class="d-flex flex-wrap align-items-center gap-2">

        <!-- User Dropdown -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="emp_id" class="form-label fw-semibold mb-1">User</label>
            <select name="emp_id" id="emp_id" class="form-select shadow-sm rounded">
                <option value="" selected>All Users</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->emp_id }}" {{ request('emp_id') == $employee->emp_id ? 'selected' : '' }}>
                        {{ $employee->emp_id }} - {{ $employee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn shadow-sm text-white" style="background-color: #FFA726;">
                <i class="fas fa-filter me-1"></i> Apply
            </button>

            @if(request('emp_id'))
            <a href="{{ url('/feedbacks') }}" class="btn btn-outline-dark shadow-sm">
                <i class="fas fa-sync-alt me-1"></i> Clear
            </a>
            @endif
        </div>
    </form>
</div>

    <!-- Feedback Table -->
     <div class="container-fluid px-3 mt-4">
    <div id="feedbackTable">
        @include('feedback.partials.feedback-table', ['feedbackList' => $feedbackList])
    </div>
    <!-- pagination links -->
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
     </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        function fetchFeedback(query = '') {
            $.ajax({
                url: "{{ route('feedback') }}",
                type: 'GET',
                data: {
                    search: query
                },
                success: function(data) {
                    $('#feedbackTable').html(data);
                },
                error: function() {
                    alert('Error fetching data.');
                }
            });
        }

        $('#searchBtn').click(function() {
            let query = $('#searchInput').val();
            fetchFeedback(query);
        });

        // Optional: live search as user types
        $('#searchInput').on('keyup', function(e) {
            if (e.keyCode === 13) { // on Enter key pressed
                fetchFeedback($(this).val());
            }
        });
    });
</script>
@endsection