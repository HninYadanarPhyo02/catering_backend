@extends('layouts.app')
@section('title','Customers Management')
@section('content')
<div class="container mt-5">
    <h4 class="mb-4">Customer Management</h4>

    {{-- Success message --}}
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    {{-- Error message --}}
    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Add New Customer --}}
    <form action="{{ route('customers.store') }}" method="POST" class="mb-4">
        <h1>Employee Management</h1>
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="name" class="form-label">Customer Name</label>
                <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
            </div>
            <div class="col-md-4">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Add Customer</button>
            </div>
        </div>
    </form>

    
    {{-- Customer List --}}
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>Emp_id</th>
                <th>Name</th>
                <th>Email</th>
                <th style="width: 160px">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customers as $index => $customer)
            <tr>
                <td>{{ $customer->emp_id }}</td>
                <td>{{ $customer->name }}</td>
                <td>{{ $customer->email }}</td>
                <td>
                    <!-- <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form> -->
                    <div class="d-flex gap-1">
                        <a href="{{ route('customers.edit', $customer->emp_id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <!-- <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-warning">Edit</a> -->
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No customers found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection