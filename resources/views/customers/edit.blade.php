@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow rounded">
                <div class="card-header" style="background-color: #264653; color: white;">
                    <h4 class="mb-0">Edit Employee Info</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('customers.update', $customer->emp_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Name Field --}}
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                class="form-control"
                                value="{{ old('name', $customer->name) }}"
                                required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email Field --}}
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                value="{{ old('email', $customer->email) }}"
                                required>
                            @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Role Field --}}
                        <div class="mb-4">
                            <label for="role" class="form-label fw-semibold">Role</label>
                            <select
                                name="role"
                                id="role"
                                class="form-select"
                                required>
                                <option value="employee" {{ (old('role', $customer->role) === 'employee') ? 'selected' : '' }}>Employee</option>
                                <option value="admin" {{ (old('role', $customer->role) === 'admin') ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn" style="background-color: #2A9D8F; color: white;">
                                <i class="bi bi-check-circle me-1"></i> Update
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection