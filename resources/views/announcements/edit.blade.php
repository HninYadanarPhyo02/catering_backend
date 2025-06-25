@extends('layouts.app')

@section('title', 'Edit Announcement')

@section('content')
<div class="container py-5" style="max-width: 600px;">
  <div class="card shadow rounded">
    <div class="card-header" style="background-color: #264653; color: white;">
      <h4 class="mb-0">Edit Announcement</h4>
    </div>

    <div class="card-body">
      <form action="{{ route('announcements.update', $announcement->id) }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="mb-3">
          <label for="title" class="form-label fw-semibold">Title</label>
          <input
            type="text"
            name="title"
            id="title"
            class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $announcement->title) }}"
            placeholder="Enter announcement title"
            required>
          @error('title')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-3">
          <label for="date" class="form-label fw-semibold">Date</label>
          <input
            type="date"
            name="date"
            id="date"
            class="form-control @error('date') is-invalid @enderror"
            value="{{ old('date', $announcement->date) }}"
            required>
          @error('date')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="mb-4">
          <label for="text" class="form-label fw-semibold">Text</label>
          <textarea
            name="text"
            id="text"
            rows="5"
            class="form-control @error('text') is-invalid @enderror"
            placeholder="Enter announcement details"
            required>{{ old('text', $announcement->text) }}</textarea>
          @error('text')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>

        <div class="d-flex justify-content-between">
          <a href="{{ route('announcement') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left-circle me-1"></i> Cancel
          </a>

          <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
            <i class="bi bi-check-circle me-1"></i> Save Announcement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection