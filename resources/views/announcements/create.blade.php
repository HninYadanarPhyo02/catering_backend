@extends('layouts.app')

@section('title', 'Add New Announcement')

@section('content')
<div class="container py-5" style="max-width: 600px;">
  <div class="card shadow rounded">
    <div class="card-header" style="background-color: #264653; color: white;">
      <h4 class="mb-0">📢 Add New Announcement</h4>
    </div>

    <div class="card-body p-4">
      <form action="{{ route('announcements.store') }}" method="POST" class="needs-validation" novalidate>
        @csrf

        <!-- Title Field -->
        <div class="mb-3">
          <label for="title" class="form-label fw-semibold">Title</label>
          <input
            type="text"
            name="title"
            id="title"
            class="form-control rounded shadow-sm @error('title') is-invalid @enderror"
            style="background-color: #f8f9fa;"
            value="{{ old('title') }}"
            placeholder="Title for your announcement"
            required>
          @error('title')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <!-- Date Field -->
        <div class="mb-3">
          <label for="date" class="form-label fw-semibold">Date</label>
          <input
            type="date"
            name="date"
            id="date"
            class="form-control rounded shadow-sm @error('date') is-invalid @enderror"
            style="background-color: #f8f9fa;"
            value="{{ old('date') }}"
            required>
          @error('date')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <!-- Text Field -->
        <div class="mb-4">
          <label for="text" class="form-label fw-semibold">Message</label>
          <textarea
            name="text"
            id="text"
            rows="5"
            class="form-control rounded shadow-sm @error('text') is-invalid @enderror"
            style="background-color: #f8f9fa;"
            placeholder="Write your announcement here..."
            required>{{ old('text') }}</textarea>
          @error('text')
            <div class="text-danger small mt-1">{{ $message }}</div>
          @enderror
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-end gap-3">
          <a href="{{ route('announcement') }}" class="btn btn-outline-secondary rounded-pill px-4" style="border-color: #264653; color: #264653;">
            Cancel
          </a>
          <button type="submit" class="btn rounded-pill px-4" style="background-color: #2A9D8F; color: white;">
            Add Announcement
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection