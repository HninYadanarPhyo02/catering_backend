@extends('layouts.app')

@section('title', 'Add New Announcement')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-5">
                    <h4 class="mb-4 fw-bold" style="color: #264653;">📢 Add New Announcement</h4>

                    <form action="{{ route('announcements.store') }}" method="POST" class="needs-validation" novalidate>
                        @csrf

                        <!-- Title Field -->
                        <div class="mb-4">
                            <label for="title" class="form-label fw-semibold text-muted">Title</label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                class="form-control @error('title') is-invalid @enderror rounded-2 shadow-sm"
                                style="background-color: #f8f9fa;"
                                value="{{ old('title') }}"
                                placeholder="Title for your announcement"
                                required>
                            @error('title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date Field -->
                        <div class="mb-4">
                            <label for="date" class="form-label fw-semibold text-muted">Date</label>
                            <input
                                type="date"
                                name="date"
                                id="date"
                                class="form-control @error('date') is-invalid @enderror rounded-2 shadow-sm"
                                style="background-color: #f8f9fa;"
                                value="{{ old('date') }}"
                                required>
                            @error('date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Text Field -->
                        <div class="mb-4">
                            <label for="text" class="form-label fw-semibold text-muted">Message</label>
                            <textarea
                                name="text"
                                id="text"
                                rows="5"
                                class="form-control @error('text') is-invalid @enderror rounded-2 shadow-sm"
                                style="background-color: #f8f9fa;"
                                required
                                placeholder="Write your announcement here...">{{ old('text') }}</textarea>
                            @error('text')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('announcement') }}" class="btn btn-outline-secondary rounded-pill px-4 d-flex align-items-center gap-2 shadow-sm" style="border-color: #264653; color: #264653;">
                                Cancel
                            </a>
                            <button type="submit" class="btn rounded-pill px-4" style="background-color: #2A9D8F; color: #fff;">
                                Add Announcement
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection