@extends('layouts.app')

@section('title', 'All Feedback')

@section('content')
<div class="container mt-5">
    <form action="{{ url('/feedbacks') }}" method="GET" class="card shadow-sm border-0 p-4 mb-4">
  <h4 class="fw-bold mb-4" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <i class="bi bi-chat-left-text me-2"></i> Employee Feedback
  </h4>

  <div class="row g-3 align-items-end">
    <form method="GET" action="{{ url('/feedbacks') }}">
  <div class="row">
    <div class="col-md-3">
      <input
        type="text"
        id="searchInput"
        name="search"
        value="{{ request('search') }}"
        class="form-control"
        placeholder="Enter Employee ID or Name"
      />
    </div>
    <div class="col-md-auto d-flex gap-2">
      <button type="submit" class="btn px-4" style="background-color: #2A9D8F; color: white;">
        <i class="fas fa-search me-1"></i> Search
      </button>

      @if(request('search'))
      <a href="{{ url('/feedbacks') }}" class="btn btn-outline-secondary px-3" style="color: #264653; border-color: #264653;">
        <i class="bi bi-x-circle me-1"></i> Clear
      </a>
      @endif
    </div>
  </div>
</form>

  </div>
</form>




    <!-- Feedback Table -->
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
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function fetchFeedback(query = '') {
        $.ajax({
            url: "{{ route('feedback') }}",
            type: 'GET',
            data: { search: query },
            success: function (data) {
                $('#feedbackTable').html(data);
            },
            error: function () {
                alert('Error fetching data.');
            }
        });
    }

    $('#searchBtn').click(function () {
        let query = $('#searchInput').val();
        fetchFeedback(query);
    });

    // Optional: live search as user types
    $('#searchInput').on('keyup', function (e) {
        if (e.keyCode === 13) { // on Enter key pressed
            fetchFeedback($(this).val());
        }
    });
});
</script>
@endsection
