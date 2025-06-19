@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="container mt-5" style="max-width: 600px;">
    <!-- ✅ FORM START -->
    <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" class="card shadow-sm border-0 p-4 mb-4" novalidate>
        @csrf
        @method('PUT')

        <h4 class="fw-bold mb-4" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <i class="bi bi-pencil-square me-2"></i> Edit Announcement
        </h4>

        <div class="row g-4">
            <!-- Title Field -->
            <div class="col-md-6">
                <label for="title" class="form-label fw-semibold text-muted">Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $announcement->title) }}"
                    placeholder="Enter announcement title"
                    required>
                @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Date Field -->
            <div class="col-md-6">
                <label for="date" class="form-label fw-semibold text-muted">Date</label>
                <input
                    type="date"
                    name="date"
                    id="date"
                    class="form-control @error('date') is-invalid @enderror"
                    value="{{ old('date', $announcement->date) }}"
                    required>
                @error('date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Text Field -->
            <div class="col-12">
                <label for="text" class="form-label fw-semibold text-muted">Text</label>
                <textarea
                    name="text"
                    id="text"
                    rows="5"
                    class="form-control @error('text') is-invalid @enderror"
                    placeholder="Enter announcement details"
                    required>{{ old('text', $announcement->text) }}</textarea>
                @error('text')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Actions -->
            <div class="col-12 d-flex gap-3 mt-3">
                <button type="submit" class="btn px-4" style="background-color: #2A9D8F; color: white;">
                    <i class="bi bi-check-circle me-1"></i> Save Announcement
                </button>
                <a href="{{ route('announcement') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
                    <i class="bi bi-arrow-clockwise"></i> Cancel
                </a>
            </div>
        </div>
    </form>

    <!-- ✅ FORM END -->
</div>
@endsection