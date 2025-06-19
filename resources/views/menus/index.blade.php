@extends('layouts.app') {{-- Make sure you have a layout file or change this as needed --}}

@section('content')
<div class="container mt-5">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif



    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Create New Menu --}}
    <form action="{{ route('menus.store') }}" method="POST" class="card shadow-sm border-0 p-4 mb-4">
        <h4 class="fw-bold mb-4" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
            <i class="bi bi-plus-circle me-2"></i> Create New Menu
        </h4>

        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="name" class="form-label fw-semibold text-muted">Menu Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter menu name" required>
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn px-4" style="background-color: #2A9D8F; color: white;">
                    <i class="fas fa-plus-circle me-1"></i> Add Menu
                </button>
            </div>
        </div>
    </form>



    {{-- Menu Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle shadow-sm rounded">
            <thead class="table-light border-bottom">
                <tr class="text-secondary">
                    <th scope="col"> # </th>
                    <th scope="col">Menu Name</th>
                    <th scope="col" class="text-center" style="width: 220px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($menus as $index => $menu)
                
                <tr>
                    <!-- <td class="fw-semibold">{{ $menu->id }}</td> -->
                     <td class="ps-3 text-muted">{{ $index + 1 }}</td>
                    <td>{{ $menu->name }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            {{-- Edit Form --}}
                            <form action="{{ route('menus.update', $menu->id) }}" method="POST" class="d-flex align-items-center gap-2 mb-0">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" class="form-control form-control-sm w-auto" value="{{ $menu->name }}" required>
                                <button type="submit" class="btn btn-sm"
                                    style="color: #2A9D8F; border: 1px solid #2A9D8F; background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='#2A9D8F'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#2A9D8F';">
                                   <i class="fas fa-edit"></i>  Update
                                </button>
                            </form>

                            {{-- Delete Form --}}
                            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="mb-0"
                                onsubmit="return confirm('Are you sure to delete ID {{ $menu->id }} ({{ $menu->name }})?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm"
                                    style="color: #E76F51; border: 1px solid #E76F51; background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='#E76F51'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#E76F51';">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No menu items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- pagination links -->
@if ($menus->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($menus->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link rounded-pill border-0 text-white" href="{{ $menus->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                    </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($menus->links()->elements[0] as $page => $url)
                    @if ($page == $menus->currentPage())
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
                @if ($menus->hasMorePages())
                    <li class="page-item">
                        <a class="page-link rounded-pill border-0 text-white" href="{{ $menus->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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