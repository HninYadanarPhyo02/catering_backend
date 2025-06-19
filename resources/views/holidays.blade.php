@extends('layouts.app')
@section('title','Menu')
@section('content')
<div class="container mt-5">

    <!-- <h4 class="mb-4">Menu Management</h4> -->
    {{-- Add New Holiday --}}
<form action="{{ route('holidays.store') }}" method="POST" class="card p-4 shadow-sm border-0 mb-4">
    @csrf
    <h4 class="fw-bold mb-4" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
        <i class="bi bi-plus-circle me-2"></i> Add New Holiday
    </h4>

    <div class="row g-3">
        <div class="col-md-4">
            <label for="name" class="form-label fw-semibold">Holiday Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Christmas" value="{{ old('name') }}" required>
        </div>

        <div class="col-md-4">
            <label for="date" class="form-label fw-semibold">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        <div class="col-md-4">
            <label for="description" class="form-label fw-semibold">Description</label>
            <input type="text" name="description" id="description" class="form-control" placeholder="Optional" value="{{ old('description') }}">
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="submit" class="btn px-4" style="background-color: #2A9D8F; color: white;">
            <i class="bi bi-plus-circle me-1"></i> Add Holiday
        </button>
    </div>
</form>

<!-- Holiday Table -->
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
                    <a href="{{ route('holidays.edit', $holiday->h_id) }}" 
                       class="btn btn-sm" 
                       style="color: #2A9D8F; border: 1px solid #2A9D8F; background-color: transparent;"
                       onmouseover="this.style.backgroundColor='#2A9D8F'; this.style.color='white';"
                       onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2A9D8F';"
                       title="Edit Holiday" aria-label="Edit holiday {{ $holiday->name }}">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <form action="{{ route('holidays.destroy', $holiday->h_id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this holiday?')" class="m-0 p-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" 
                                style="color: #E76F51; border: 1px solid #E76F51; background-color: transparent;"
                                onmouseover="this.style.backgroundColor='#E76F51'; this.style.color='white';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='#E76F51';"
                                title="Delete Holiday" aria-label="Delete holiday {{ $holiday->name }}">
                            <i class="fas fa-trash-alt"></i> Delete
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
<!-- pagination links -->
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


@endsection