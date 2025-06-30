@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mt-5">

    <div class="card shadow-sm rounded mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('announcement') }}">
                <!-- Header with count -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-2">
                    <h4 class="fw-bold d-flex justify-content-between align-items-center w-100 flex-wrap"
                        style="color: #2A9D8F;">

                        <span><i class="fas fa-bullhorn me-2"></i> Announcements</span>
                        @php
                        use Carbon\Carbon;
                        @endphp

                        <span class="badge" style="background-color:; color:  #2A9D8F; font-size: 0.95rem;">
                            for {{ Carbon::now()->format('F') }} : {{ $monthlyAnnouncementCount }}
                        </span>

                    </h4>
                </div>

                <!-- Filter and Actions -->
                <div class="row g-2 align-items-center">
                    <!-- Search input -->
                    <div class="col-sm-5 col-md-4">
                        <input type="text" name="search"
                            class="form-control form-control-sm shadow-sm"
                            placeholder="Search by Title, Date or Text"
                            value="{{ request('search') }}"
                            style="border-color: #264653;" />
                    </div>

                    <!-- Search button -->
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm text-white px-3"
                            style="background-color: #2A9D8F;">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>

                    <!-- Clear button -->
                    @if(request('search'))
                    <div class="col-auto">
                        <a href="{{ route('announcement') }}" class="btn btn-sm btn-outline-secondary px-3"
                            style="color: #264653; border-color: #264653;">
                            <i class="fas fa-times-circle me-1"></i> Clear
                        </a>
                    </div>
                    @endif

                    <!-- Add Announcement button -->
                    <div class="col-auto ms-auto">
                        <a href="{{ route('announcements.create') }}" class="btn btn-sm text-white px-3"
                            style="background-color: #2A9D8F;">
                            <i class="fas fa-plus me-1"></i> Add Announcement
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>


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
                        <a href="{{ route('announcements.show', $announcement->id) }}"
                            class="btn btn-sm"
                            style="color:rgb(0, 133, 117); border: 1px solid rgb(0, 133, 117); background-color: transparent;"
                            onmouseover="this.style.backgroundColor='rgb(0, 133, 117)'; this.style.color='white';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(0, 133, 117)';">
                            <i class="far fa-eye"></i>
                        </a>
                        <a href="{{ route('announcements.edit', $announcement->id) }}"
                            class="btn btn-sm"
                            style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                            onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                            onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';">
                            <i class="fas fa-edit"></i>
                        </a>
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

    {{-- Custom Styled Pagination --}}
    @if ($announcements->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($announcements->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $announcements->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Page Numbers --}}
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

                {{-- Next Page --}}
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
@endsection