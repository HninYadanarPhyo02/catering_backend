@if($feedbackList->isEmpty())
<div class="card shadow-sm rounded-3 p-4 text-center" style="background-color: #fff3e0;">
    <i class="fas fa-inbox fa-2x mb-2 text-warning"></i>
    <p class="mb-0 fw-semibold text-muted">No feedback records found.</p>
</div>
@else
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header" style="background-color: #FFE0B2; border-bottom: 1px solid #dee2e6;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="bi bi-chat-left-dots-fill me-2 text-warning"></i> Feedback Summary
            </h5>
            <span class="badge bg-warning text-dark">{{ $feedbackList->count() }} Users</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #FFF3E0;" class="text-center text-uppercase">
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Total Feedback</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($feedbackList as $summary)
                <tr>
                    <td class="fw-semibold text-dark">{{ $summary->emp_id }}</td>
                    <td class="text-dark">{{ $summary->employee->name ?? '-' }}</td>
                    <td class="fw-semibold text-dark">{{ $summary->total_feedback }}</td>
                    <td>
                        <a href="{{ route('feedback.detail', $summary->emp_id) }}" 
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
@endif
