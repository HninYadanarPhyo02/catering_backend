@extends('layouts.app')

@section('content')
<div class="container mt-5">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Business-Styled Employee Creation Form -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header" style="background-color: #264653; color: white;">
            <h5 class="mb-0">Employee Management</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('customers.store') }}" method="POST" novalidate>
                @csrf
                <div class="row g-4 align-items-center">
                    <!-- Employee Name -->
                    <div class="col-md-5">
                        <label for="name" class="form-label fw-semibold">Employee Name</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="e.g. Jane Doe" required>
                    </div>

                    <!-- Employee Email -->
                    <div class="col-md-5">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="e.g. jane@company.com" required>
                    </div>

                    <!-- Submit -->
                    <div class="col-md-2 d-grid">
                        <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                            <i class="fas fa-user-plus me-1"></i> Add Employee

                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <div class="card shadow rounded">
        <div class="card-body p-4">

            <!-- Role Filter Form -->
            <form method="GET" action="{{ route('customers.index') }}"
                class="mb-5 p-4 border-0 rounded-4 shadow-sm bg-white">

                <h5 class="fw-bold mb-4 pb-2 border-bottom d-flex justify-content-between align-items-center"
                    style="color: #2A9D8F; border-color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">

                    <span>
                        <i class="fas fa-filter me-2"></i> Filter by Role
                    </span>

                    <span>
                        <span class="badge" style="font-size: 0.95rem;  color: #2A9D8F;">Employees : {{ $employeeCount }}</span>
                        <span class="badge" style="font-size: 0.95rem;  color:#264653;">Admins : {{ $adminCount }}</span>
                    </span>
                </h5>



                <div class="row g-3 align-items-end">
                    <!-- Role Dropdown -->
                    <div class="col-md-6">
                        <label for="roleFilter" class="form-label fw-semibold text-muted">Select Role</label>
                        <select name="role" id="roleFilter" class="form-select shadow-sm rounded">
                            <option value="" selected disabled>All Roles</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            <!-- Add other roles as needed -->
                        </select>
                    </div>

                    <!-- Filter and Clear Buttons -->
                    <div class="col-md-6 d-flex flex-wrap gap-2 mt-2">
                        <button type="submit"
                            class="btn px-4 py-2 rounded-3 shadow-sm text-white"
                            style="background-color: #2A9D8F; border: none;">
                            <i class="fas fa-filter me-1"></i> Apply Filter
                        </button>

                        @if(request('role'))
                        <a href="{{ route('customers.index') }}"
                            class="btn btn-outline-secondary px-4 d-flex align-items-center gap-2 shadow-sm">
                            <i class="fas fa-sync-alt"></i> Clear
                        </a>
                        @endif
                    </div>
                </div>
            </form>


            <!-- employees table -->
            <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                <thead class="table-light border-bottom">
                    <tr class="text-secondary text-center">
                        <th scope="col">Emp ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                    <tr class="text-center">
                        <td class="fw-semibold">{{ $customer->emp_id }}</td>
                        <td class="text-start">{{ $customer->name }}</td>
                        <td class="text-start">{{ $customer->email }}</td>
                        <td class="text-start">{{ $customer->role }}</td>
                        <td>
                            <a href="{{ route('customers.edit', $customer->emp_id) }}"
                                class="btn btn-sm"
                                style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                                onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                                onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';">
                                <i class="fas fa-edit"></i>
                            </a>

                            <form action="{{ route('customers.destroy', $customer->emp_id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Are you sure to delete {{$customer->role}}: {{ $customer->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm"
                                    style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='#rgb(182, 48, 14)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='#rgb(182, 48, 14)';">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    <!-- pagination links -->
    @if ($customers->hasPages())
    <div class="d-flex justify-content-end mt-4">
        <nav>
            <ul class="pagination pagination-sm mb-0">
                {{-- Previous Page --}}
                @if ($customers->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $customers->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                </li>
                @endif

                {{-- Page Numbers --}}
                @foreach ($customers->links()->elements[0] as $page => $url)
                @if ($page == $customers->currentPage())
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
                @if ($customers->hasMorePages())
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-white" href="{{ $customers->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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
<style>
    .badge-employee {
        background-color: #2A9D8F;
        color: white;
    }

    .badge-admin {
        background-color: #264653;
        color: white;
    }
</style>