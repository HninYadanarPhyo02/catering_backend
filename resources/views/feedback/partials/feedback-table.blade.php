<!-- Feedback Summary -->
@if($feedbackList->isEmpty())
<div class="alert alert-warning text-center shadow-sm rounded-3">
    No feedback records found.
</div>
@else
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-header bg-white border-bottom fw-semibold text-secondary">
        <i class="bi bi-chat-left-dots-fill me-2 text-primary"></i> Feedback Summary
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-center text-uppercase">
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Total Feedback</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($feedbackList as $summary)
                <tr class="text-center">
                    <td class="fw-semibold text-secondary">{{ $summary->emp_id }}</td>
                    <td>{{ $summary->employee->name ?? '-' }}</td>
                    <td>{{ $summary->total_feedback }}</td>
                    <td>
                        <a href="{{ route('feedback.detail', $summary->emp_id) }}"
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
@endif
