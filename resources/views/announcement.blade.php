@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container-fluid px-3 mt-4">

    {{-- Search & Add Announcement --}}
    <div class="card shadow-sm rounded-3 mb-4 p-3" 
     style="max-width: 700px; width: 100%; margin: auto; background-color: #fff3e6;">
    <form method="GET" action="{{ route('announcement') }}" class="d-flex flex-wrap gap-3 align-items-end">
        
        <div class="w-100 mb-2">
            <h5 class="fw-bold mb-0" style="color: #e76f51;">
                <i class="fas fa-bullhorn me-2"></i> Announcements
            </h5>
        </div>

        <!-- Search Input -->
        <div class="flex-grow-1 flex-md-auto" style="min-width: 200px;">
            <label for="search" class="form-label fw-semibold mb-1">Search</label>
            <input type="text" name="search" id="search"
                   class="form-control shadow-sm rounded"
                   placeholder="Title, Date or Text"
                   value="{{ request('search') }}">
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2 flex-wrap ms-auto align-items-center">
            <button type="submit" class="btn text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f4a261;">
                <i class="fas fa-search"></i> Filter
            </button>

            @if(request()->hasAny(['search','month','year']))
            <a href="{{ route('announcement') }}" class="btn btn-outline-secondary shadow-sm d-flex align-items-center gap-1">
                <i class="fas fa-times-circle"></i> Clear
            </a>
            @endif

            <!-- Add Announcement Button -->
            <button type="button" class="btn text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f4a261;"
                    data-bs-toggle="modal" data-bs-target="#addAnnouncementModal">
                <i class="fas fa-plus-circle"></i> Add Announcement
            </button>

            <span class="badge bg-secondary align-self-center">Total Announcements: {{ $monthlyAnnouncementCount }}</span>
        </div>

    </form>
</div>
<div class="container-fluid px-3 mt-4">
    @if($announcements->isEmpty())
        <div class="alert alert-warning shadow-sm rounded text-center">
            <i class="bi bi-exclamation-circle me-2"></i> No announcements found.
        </div>
    @else
        <div class="table-responsive shadow-sm rounded">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase">
                    <tr>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 20%;">Title</th>
                        <th>Text</th>
                        <th style="width: 18%;">Posted At</th>
                        <th style="width: 18%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($announcements as $announcement)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($announcement->date)->format('F j, Y') }}</td>
                        <td>{{ Str::limit($announcement->title, 40) }}</td>
                        <td>{{ Str::limit($announcement->text, 60) }}</td>
                        <td>{{ $announcement->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-nowrap">
                            <!-- View Button -->
                            <button
                                type="button"
                                class="btn btn-sm"
                                style="color:rgb(0, 133, 117); border: 1px solid rgb(0, 133, 117); background-color: transparent;"
                                title="View Announcement"
                                data-bs-toggle="modal"
                                data-bs-target="#viewAnnouncementModal"
                                data-id="{{ $announcement->id }}"
                                data-title="{{ $announcement->title }}"
                                data-date="{{ $announcement->date }}"
                                data-text="{{ $announcement->text }}"
                                data-created_at="{{ $announcement->created_at->format('F j, Y \a\t h:i A') }}"
                                onmouseover="this.style.backgroundColor='rgb(0, 133, 117)'; this.style.color='white';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(0, 133, 117)';"
                            >
                                <i class="far fa-eye"></i>
                            </button>

                            <!-- Edit Button -->
                            <button
                                type="button"
                                class="btn btn-sm"
                                style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                                title="Edit Announcement"
                                data-bs-toggle="modal"
                                data-bs-target="#editAnnouncementModal"
                                data-id="{{ $announcement->id }}"
                                data-title="{{ $announcement->title }}"
                                data-date="{{ $announcement->date }}"
                                data-text="{{ $announcement->text }}"
                                onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';"
                            >
                                <i class="fas fa-edit"></i>
                            </button>

                            {{-- Delete Form --}}
                            <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST"
                                class="d-inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this announcement ({{$announcement->title}}) on {{$announcement->date}}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm"
                                    style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
    {{-- Pagination --}}
    @if ($announcements->hasPages())
        <div class="d-flex justify-content-end mt-4">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    {{-- Previous --}}
                    @if ($announcements->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link rounded-pill border-0 text-white" href="{{ $announcements->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                    </li>
                    @endif

                    {{-- Page numbers --}}
                    @foreach ($announcements->links()->elements[0] as $page => $url)
                        @if ($page == $announcements->currentPage())
                        <li class="page-item active">
                            <span class="page-link rounded-pill border-0 text-white" style="background-color: #264653;">{{ $page }}</span>
                        </li>
                        @else
                        <li class="page-item">
                            <a class="page-link rounded-pill border-0 text-dark" href="{{ $url }}" style="background-color: #E9F7F6;">{{ $page }}</a>
                        </li>
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if ($announcements->hasMorePages())
                    <li class="page-item">
                        <a class="page-link rounded-pill border-0 text-white" href="{{ $announcements->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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

{{-- VIEW MODAL --}}
<div class="modal fade" id="viewAnnouncementModal" tabindex="-1" aria-labelledby="viewAnnouncementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-sm rounded-4 border-0" style="background-color: #fff3e6;">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background-color: #e76f51; color: white; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
        <h5 class="modal-title fw-bold" id="viewAnnouncementModalLabel">
          <i class="fas fa-bullhorn me-2"></i> Announcement Details
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <h5 id="viewTitle" class="fw-bold mb-3" style="color: #264653;"></h5>
        <p><strong>Date:</strong> <span id="viewDate" class="fw-semibold"></span></p>
        <p id="viewText" style="white-space: pre-line; color: #264653;"></p>
        <p class="text-muted fst-italic small">Posted at: <span id="viewCreatedAt"></span></p>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer border-0">
        <button type="button" class="btn shadow-sm" style="background-color: #f4a261; color: white;" data-bs-dismiss="modal">
          Close
        </button>
      </div>

    </div>
  </div>
</div>


{{-- EDIT MODAL --}}
<div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-sm rounded-4 border-0" style="background-color: #fff3e6;">
      
      <!-- Modal Header -->
      <div class="modal-header" style="background-color: #e76f51; color: white; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
        <h5 class="modal-title fw-bold" id="editAnnouncementModalLabel">
          <i class="fas fa-edit me-2"></i> Edit Announcement
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Form -->
      <form id="editAnnouncementForm" method="POST" action="">
        @csrf
        @method('PUT')

        <!-- Modal Body -->
        <div class="modal-body">
          <div class="mb-3">
            <label for="editTitle" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input
              type="text"
              name="title"
              id="editTitle"
              class="form-control shadow-sm rounded"
              placeholder="Enter title"
              required>
          </div>

          <div class="mb-3">
            <label for="editDate" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
            <input
              type="date"
              name="date"
              id="editDate"
              class="form-control shadow-sm rounded"
              required>
          </div>

          <div class="mb-3">
            <label for="editText" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
            <textarea
              name="text"
              id="editText"
              rows="5"
              class="form-control shadow-sm rounded"
              placeholder="Enter message"
              required></textarea>
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer border-0">
          <button type="button" class="btn shadow-sm" style="background-color: #f4a261; color: white;" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i> Cancel
          </button>
          <button type="submit" class="btn shadow-sm" style="background-color: #2A9D8F; color: white;">
            <i class="bi bi-check-lg me-1"></i> Update Announcement
          </button>
        </div>

      </form>
    </div>
  </div>
</div>

{{-- ADD MODAL --}}
<div class="modal fade" id="addAnnouncementModal" tabindex="-1" aria-labelledby="addAnnouncementModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow-sm rounded-4 border-0" style="background-color: #fff3e6;">

      <!-- Modal Header -->
      <div class="modal-header" style="background-color: #e76f51; color: white; border-top-left-radius: 0.75rem; border-top-right-radius: 0.75rem;">
        <h5 class="modal-title fw-bold" id="addAnnouncementModalLabel">
          <i class="bi bi-plus-circle me-2"></i> Add New Announcement
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- Form -->
      <form id="addAnnouncementForm" action="{{ route('announcements.store') }}" method="POST" novalidate>
        @csrf
        <div class="modal-body">

          <!-- Title -->
          <div class="mb-3">
            <label for="addTitle" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
            <input
              type="text"
              name="title"
              id="addTitle"
              class="form-control rounded shadow-sm @error('title') is-invalid @enderror"
              placeholder="Title for your announcement"
              value="{{ old('title') }}"
              required>
            @error('title')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <!-- Date -->
          <div class="mb-3">
            <label for="addDate" class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
            <input
              type="date"
              name="date"
              id="addDate"
              class="form-control rounded shadow-sm @error('date') is-invalid @enderror"
              value="{{ old('date') }}"
              required>
            @error('date')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <!-- Message -->
          <div class="mb-4">
            <label for="addText" class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
            <textarea
              name="text"
              id="addText"
              rows="5"
              class="form-control rounded shadow-sm @error('text') is-invalid @enderror"
              placeholder="Write your announcement here..."
              required>{{ old('text') }}</textarea>
            @error('text')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer border-0">
          <button type="button" class="btn shadow-sm" style="background-color: #f4a261; color: white;" data-bs-dismiss="modal">
            <i class="bi bi-x-lg me-1"></i> Cancel
          </button>
          <button type="submit" class="btn shadow-sm" style="background-color: #2A9D8F; color: white;">
            <i class="bi bi-check-lg me-1"></i> Add Announcement
          </button>
        </div>

      </form>
    </div>
  </div>
</div>


{{-- Modal Scripts --}}
<script>
  // View Modal populate
  var viewModal = document.getElementById('viewAnnouncementModal');
  viewModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var title = button.getAttribute('data-title');
    var date = button.getAttribute('data-date');
    var text = button.getAttribute('data-text');
    var createdAt = button.getAttribute('data-created_at');

    viewModal.querySelector('#viewTitle').textContent = title;
    viewModal.querySelector('#viewDate').textContent = new Date(date).toLocaleDateString(undefined, {year:'numeric', month:'long', day:'numeric'});
    viewModal.querySelector('#viewText').textContent = text;
    viewModal.querySelector('#viewCreatedAt').textContent = createdAt;
  });

  // Edit Modal populate
  var editModal = document.getElementById('editAnnouncementModal');
  editModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var id = button.getAttribute('data-id');
    var title = button.getAttribute('data-title');
    var date = button.getAttribute('data-date');
    var text = button.getAttribute('data-text');

    var form = editModal.querySelector('#editAnnouncementForm');
    form.action = '/announcements/' + id;

    form.querySelector('#editTitle').value = title;
    form.querySelector('#editDate').value = date;
    form.querySelector('#editText').value = text;
  });

  // Auto-open Add modal if validation errors exist on create
  @if ($errors->any() && old('_method') != 'PUT')
    document.addEventListener('DOMContentLoaded', function () {
      var addModal = new bootstrap.Modal(document.getElementById('addAnnouncementModal'));
      addModal.show();
    });
  @endif
</script>

@endsection
