@extends('layouts.app')

@section('title','Holidays')

@section('content')
<div class="container-fluid px-3 mt-4">

    {{-- Add New Holiday Form --}}
   <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff3e6;">
    <form action="{{ route('holidays.store') }}" method="POST" class="d-flex flex-wrap gap-3 align-items-end">
        @csrf
        <div class="w-100 mb-2">
            <h5 class="fw-bold mb-0" style="color: #e76f51;">
                <i class="bi bi-plus-circle me-2"></i> Add New Holiday
            </h5>
        </div>

        <div class="flex-grow-1 flex-md-auto" style="min-width: 150px;">
            <label for="name" class="form-label fw-semibold mb-1">Holiday Name</label>
            <input type="text" name="name" id="name" class="form-control shadow-sm rounded" placeholder="e.g. Christmas" value="{{ old('name') }}" required>
        </div>

        <div class="flex-grow-1 flex-md-auto" style="min-width: 150px;">
            <label for="date" class="form-label fw-semibold mb-1">Date</label>
            <input type="date" name="date" id="date" class="form-control shadow-sm rounded" value="{{ old('date') }}" required>
        </div>

        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="description" class="form-label fw-semibold mb-1">Description</label>
            <input type="text" name="description" id="description" class="form-control shadow-sm rounded" placeholder="Optional" value="{{ old('description') }}">
        </div>

        <div class="d-flex gap-2 flex-wrap ms-auto">
            <button type="submit" class="btn text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f4a261;">
                <i class="bi bi-plus-circle"></i> Add Holiday
            </button>
            <span class="badge bg-secondary align-self-center">Total Holidays: {{ $holidaysCount }}</span>
        </div>
    </form>
</div>

</div>


    {{-- Holiday Table --}}
     <div class="container-fluid px-4 mt-4">
     <div class="card mb-4 shadow-sm">
    <table class="table table-hover table-striped align-middle">
        <thead class="table-secondary text-uppercase text-muted">
            <tr>
                <th>Name</th>
                <th style="width: 150px;">Date</th>
                <th>Description</th>
                <th style="width: 140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($holidays as $holiday)
            <tr>
                <td class="fw-semibold">{{ $holiday->name }}</td>
                <td>{{ \Carbon\Carbon::parse($holiday->date)->format('M d, Y') }}</td>
                <td class="text-muted">{{ $holiday->description ?? '-' }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <!-- Edit Button triggers modal -->
                        <button
                            type="button"
                            class="btn btn-sm btn-edit-holiday"
                            style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                            onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';"
                            title="Edit Holiday"
                            aria-label="Edit holiday {{ $holiday->name }}"
                            data-bs-toggle="modal"
                            data-bs-target="#editHolidayModal"
                            data-id="{{ $holiday->h_id }}"
                            data-name="{{ $holiday->name }}"
                            data-date="{{ $holiday->date }}"
                            data-description="{{ $holiday->description }}"
                        >
                            <i class="fas fa-edit"></i>
                        </button>

                        {{-- Delete Form --}}
                        <form action="{{ route('holidays.destroy', $holiday->h_id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this holiday?')" class="m-0 p-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"
                                style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';"
                                title="Delete Holiday" aria-label="Delete holiday {{ $holiday->name }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted py-4">No holidays found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
     </div>
     </div>
    {{-- Pagination links --}}
    @if ($holidays->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($holidays->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $holidays->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($holidays->links()->elements[0] as $page => $url)
                @if ($page == $holidays->currentPage())
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
                @if ($holidays->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $holidays->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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

<!-- Edit Holiday Modal -->
<div class="modal fade" id="editHolidayModal" tabindex="-1" aria-labelledby="editHolidayModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header text-white" style="background-color: #264653;">
        <h5 class="modal-title" id="editHolidayModalLabel"><i class="fas fa-edit me-2"></i> Edit Holiday</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editHolidayForm" method="POST" action="">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label for="edit_name" class="form-label fw-semibold">Holiday Name</label><span class="text-danger"> * </span>
            <input type="text" name="name" id="edit_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="edit_date" class="form-label fw-semibold">Date</label><span class="text-danger"> * </span>
            <input type="date" name="date" id="edit_date" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="edit_description" class="form-label fw-semibold">Description</label>
            <input type="text" name="description" id="edit_description" class="form-control">
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i> Cancel
          </button>
          <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
            <i class="bi bi-check-lg me-1"></i> Update Holiday
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Scripts --}}
<script>
  document.querySelectorAll('.btn-edit-holiday').forEach(button => {
    button.addEventListener('click', () => {
      const id = button.getAttribute('data-id');
      const name = button.getAttribute('data-name');
      const date = button.getAttribute('data-date');
      const description = button.getAttribute('data-description') ?? '';

      // Populate modal inputs
      document.getElementById('edit_name').value = name;
      document.getElementById('edit_date').value = date;
      document.getElementById('edit_description').value = description;

      // Update form action URL dynamically
      document.getElementById('editHolidayForm').action = `/holidays/${id}`;
    });
  });
</script>

@endsection
