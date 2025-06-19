@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Food Orders</h1>

    {{-- Show success message --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Create Order Form --}}
    <div class="card mb-4">
        <div class="card-header">Add New Order</div>
        <div class="card-body">
            <form action="{{ route('orders.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="food_name" class="form-label">Select Food</label>
                    <select name="food_name" id="food_id" class="form-control @error('food_id') is-invalid @enderror" required>
                        <option value="" disabled selected>Select Food</option>
                        @foreach($foods as $food)
                        <option value="{{ $food->food_id }}" {{ old('food_id') == $food->food_id ? 'selected' : '' }}>
                            {{ $food->food_name }}
                        </option>
                        @endforeach
                    </select>
                    @error('food_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="text" name="price" id="price" class="form-control" value="{{ old('price') ?? '' }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date') }}" required>
                    @error('date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Add Order</button>
            </form>
        </div>
    </div>

    {{-- Orders Table --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Food Name</th>
                <th>Price</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->food_name }}</td>
                <td>${{ number_format($order->price, 2) }}</td>
                <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary btn-sm">Edit</a>

                    <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this order?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection