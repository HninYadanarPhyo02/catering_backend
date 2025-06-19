@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <form action="{{ route('customers.update', $customer->emp_id) }}" method="POST" class="card shadow-sm border-0 p-4 rounded-4 bg-white">
                    <h4 class="mb-4 text-center fw-bold" style="color: #2A9D8F; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                        ✏️ Update Employee Info
                    </h4>


                    @csrf
                    @method('PUT')

                    <!-- Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary">Full Name</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control form-control-lg rounded-3 shadow-sm"
                            value="{{ $customer->name }}"
                            required>
                    </div>

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-secondary">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control form-control-lg rounded-3 shadow-sm"
                            value="{{ $customer->email }}"
                            required>
                    </div>
                    <!-- Role Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-semibold text-secondary">Role</label>
                        <select name="role" class="form-select">
                        <option value="employee" {{ $customer->role === 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="admin" {{ $customer->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between gap-3">
                        <button type="submit" class="btn px-4 shadow-sm" style="background-color: #2A9D8F; color: white;">
                            <i class="bi bi-check-circle me-1"></i> Update
                        </button>

                        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary px-4 shadow-sm">
                            <i class="bi bi-x-circle me-1"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection