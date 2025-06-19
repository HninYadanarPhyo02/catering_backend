@extends('layouts.app')

@section('title', 'View Announcement')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 fw-bold text-center" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        📢 Announcement Details
    </h3>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header fw-semibold" style="background-color: #264653; color: #ffffff;">
                    Announcement #{{ $announcement->id }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Title</label>
                        <p class="fs-5 mb-0">{{ $announcement->title }}</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted fw-semibold">Date</label>
                        <p class="fs-5 mb-0">{{ \Carbon\Carbon::parse($announcement->date)->format('F j, Y') }}</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted fw-semibold">Text</label>
                        <p class="fs-6 mb-0" style="white-space: pre-line;">{{ $announcement->text }}</p>
                    </div>

                    <div class="mb-4 text-muted small fst-italic">
                        Posted at: {{ $announcement->created_at->format('F j, Y \a\t h:i A') }}
                    </div>

                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('announcements.edit', $announcement->id) }}"
                            class="btn px-4 text-white"
                            style="background-color: #F4A261;">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <a href="{{ route('announcement') }}" class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
                            <i class="bi bi-arrow-clockwise"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection