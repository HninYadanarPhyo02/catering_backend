@extends('layouts.app')
@section('title','User Management')
@section('content')
<div class="container-fluid px-3 mt-4">
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!
        <!-- Add Employee Form -->

        <!-- Import Employees via Excel File
<div class="card shadow-sm border-0 mb-5">
    <div class="card-header" style="background-color: #264653; color: white;">
        <h5 class="mb-0">Import Users (Excel)</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="row g-4 align-items-center">
                <div class="col-md-10">
                    <label for="excel_file" class="form-label fw-semibold">Select Excel File</label><span class="text-danger"> * </span>
                    <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls,.csv,.ods" required>
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                        <i class="fas fa-file-import me-1"></i> Upload
                    </button>
                </div>
            </div>
        </form>
    </div>
</div> -->
<div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 700px; width: 100%; margin: auto; background-color: #fff3e6;">
    <form action="{{ route('customers.store') }}" method="POST" novalidate class="d-flex flex-wrap gap-3 align-items-end">
        @csrf
        <div class="w-100 mb-2">
            <h5 class="fw-bold mb-0" style="color: #e76f51;">
                <i class="fas fa-users me-2"></i> Users Management
            </h5>
        </div>

        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="name" class="form-label fw-semibold mb-1">User Name</label>
            <input type="text" name="name" id="name" class="form-control shadow-sm rounded" placeholder="e.g. Jane Doe" required>
        </div>

        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
            <label for="email" class="form-label fw-semibold mb-1">Email Address</label>
            <input type="email" name="email" id="email" class="form-control shadow-sm rounded" placeholder="e.g. jane@company.com" required>
        </div>

        <div class="d-flex gap-2 flex-wrap ms-auto">
            <button type="submit" class="btn text-white shadow-sm d-flex align-items-center gap-1" style="background-color: #f4a261;">
                <i class="fas fa-user-plus"></i> Add Employee
            </button>
        </div>
    </form>
</div>

        <!-- Filters -->
        <div class="card shadow rounded">
            <div class="card-body p-4">
                <div class="card shadow-sm rounded-3 mb-4 p-3" style="max-width: 650px; width: 100%; margin: auto; background-color: #fff8f0;">
                    <form method="GET" action="{{ route('customers.index') }}" class="d-flex flex-wrap align-items-center gap-2">
                        <!-- Role Selector -->
                        <div class="flex-grow-1 flex-md-auto" style="min-width: 180px;">
                            <label for="roleFilter" class="form-label fw-semibold mb-1" style="color: #d35400;">Role</label>
                            <select name="role" id="roleFilter" class="form-select shadow-sm rounded" style="border-color: #f39c12;">
                                <option value="" selected>All Roles</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="employee" {{ request('role') == 'employee' ? 'selected' : '' }}>Employee</option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn shadow-sm" style="background-color: #f39c12; color: white;">
                                <i class="fas fa-filter me-1"></i> Apply
                            </button>
                            @if(request('role'))
                            <a href="{{ route('customers.index') }}" class="btn shadow-sm" style="background-color: #e67e22; color: white;">
                                <i class="fas fa-sync-alt me-1"></i> Clear
                            </a>
                            @endif
                        </div>

                        <!-- Counts -->
                        <div class="ms-auto d-flex gap-2 flex-wrap mt-2 mt-md-0">
                            <span class="badge" style="background-color: #27ae60; color: white;">Employees: {{ $employeeCount }}</span>
                            <span class="badge" style="background-color: #e74c3c; color: white;">Admins: {{ $adminCount }}</span>
                        </div>
                    </form>
                </div>


                <!-- User Table -->
                <table class="table table-bordered table-striped align-middle shadow-sm rounded">
                    <thead class="table-light text-center">
                        <tr>
                            <th>Emp ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Actions</th>
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
                                @if($customer->emp_id !== 'admin_01')
                                <!-- Edit Button -->
                                <button
                                    type="button"
                                    class="btn btn-sm btn-edit-customer"
                                    style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editCustomerModal"
                                    data-id="{{ $customer->emp_id }}"
                                    data-name="{{ $customer->name }}"
                                    data-email="{{ $customer->email }}"
                                    data-role="{{ $customer->role }}"
                                    title="Edit {{ $customer->name }}">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <!-- Delete Form -->
                                <form action="{{ route('customers.destroy', $customer->emp_id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Are you sure to delete {{$customer->role}}: {{ $customer->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm"
                                        style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                        onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @else
                                <!-- Disabled Edit/Delete -->
                                <button class="btn btn-sm" disabled style="color: gray; border: 1px solid gray;">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm" disabled style="color: gray; border: 1px solid gray;">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                @endif
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

        <!-- Pagination -->
        @if ($customers->hasPages())
        <div class="d-flex justify-content-end mt-4">
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if ($customers->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill text-muted" style="background-color: #E0E0E0;">&laquo;</span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link rounded-pill text-white" href="{{ $customers->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
                    </li>
                    @endif

                    @foreach ($customers->links()->elements[0] as $page => $url)
                    <li class="page-item {{ $page == $customers->currentPage() ? 'active' : '' }}">
                        <a class="page-link rounded-pill {{ $page == $customers->currentPage() ? 'text-white' : 'text-dark' }}"
                            style="background-color: {{ $page == $customers->currentPage() ? '#264653' : '#E9F7F6' }};"
                            href="{{ $url }}">{{ $page }}</a>
                    </li>
                    @endforeach

                    @if ($customers->hasMorePages())
                    <li class="page-item">
                        <a class="page-link rounded-pill text-white" href="{{ $customers->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link rounded-pill text-muted" style="background-color: #E0E0E0;">&raquo;</span>
                    </li>
                    @endif
                </ul>
            </nav>
        </div>
        @endif
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background-color: #264653;">
                <h5 class="modal-title" id="editCustomerModalLabel"><i class="fas fa-edit me-2"></i> Edit Employee Info</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label fw-semibold">Full Name</label><span class="text-danger"> * </span>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label fw-semibold">Email Address</label><span class="text-danger"> * </span>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role" class="form-label fw-semibold">Role</label>
                        <select name="role" id="edit_role" class="form-select" required>
                            <option value="employee">Employee</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                        <i class="bi bi-check-circle me-1"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Data Population Script -->
<script>
    document.querySelectorAll('.btn-edit-customer').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const email = button.dataset.email;
            const role = button.dataset.role;

            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;

            document.getElementById('editCustomerForm').action = `/customers/${id}`;
        });
    });
</script>
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