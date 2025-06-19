@extends('layouts.app')

@section('title', 'Announcements')

@section('content')
<div class="container mt-5">

    <div class="card shadow-sm rounded mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('announcement') }}"
            class="d-flex flex-wrap justify-content-between align-items-center gap-3">

            <h4 class="mb-0 fw-semibold w-100"
                style="border-bottom: 2px solid #2A9D8F; padding-bottom: 0.5rem; color: #2A9D8F;">
                Announcements
            </h4>

            <div class="d-flex gap-2 align-items-center flex-grow-1 flex-wrap mt-3">
                <input type="text" name="search" class="form-control form-control-sm rounded shadow-sm"
                    placeholder="Search by Title, Date or Text" value="{{ request('search') }}"
                    style="max-width: 320px; border-color: #264653;" />

                <button type="submit" class="btn px-4 text-white" style="background-color: #2A9D8F;">
                    <i class="fas fa-search me-1"></i> Search
                </button>

                @if(request('search'))
                    <a href="{{ route('announcement') }}" class="btn btn-outline-secondary btn-sm px-3 shadow-sm"
                        style="color: #264653; border-color: #264653;">
                        <i class="fas fa-times-circle me-1"></i> Clear
                    </a>
                @endif
            </div>

            <div class="mt-3 mt-md-0">
                <a href="{{ route('announcements.create') }}" class="btn btn-sm px-4 shadow-sm"
                    style="background-color: #2A9D8F; color: white;">
                    <i class="fas fa-plus me-1"></i> Add Announcement
                </a>
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
                                    class="btn btn-sm me-1 shadow-sm" style="background-color: #2A9D8F; color: white;">
                                    <i class="far fa-eye"></i>
                                </a>
                                <a href="{{ route('announcements.edit', $announcement->id) }}"
                                    class="btn btn-sm me-1 shadow-sm"
                                    style="background-color: #F4A261; color: black;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST"
                                    class="d-inline-block"
                                    onsubmit="return confirm('Are you sure you want to delete this announcement?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm shadow-sm"
                                        style="background-color: #E76F51; color: white;">
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
