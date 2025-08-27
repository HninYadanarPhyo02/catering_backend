@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Show success message --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Edit Order Form --}}
    <div class="container d-flex justify-content-center" style="padding-top: 60px; min-height: 100vh;">
        <div class="card shadow-sm border-0 mb-4 w-100" style="max-width: 600px;">
            <div class="card-header fw-semibold" style="background-color: #264653; color: white;">
                <i class="bi bi-pencil-square me-2"></i> Update Order
            </div>

            <div class="card-body">
                <form action="{{ route('orders.update', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Food Selection --}}
                    <div class="mb-3">
                        <label for="food_id" class="form-label fw-semibold">Select Food</label>
                        <select name="food_id" id="food_id" class="form-select @error('food_id') is-invalid @enderror" required>
                            <option value="" disabled>Select Food</option>
                            @foreach($foods as $food)
                            <option value="{{ $food->food_id }}" {{ $order->food_id == $food->food_id ? 'selected' : '' }}>
                                {{ $food->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('food_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Price (Kyat)</label>
                        <input type="text" name="price" id="price" class="form-control bg-light" value="{{ $order->price }}" readonly>
                    </div>

                    {{-- Date --}}
                    <div class="mb-4">
                        <label for="date" class="form-label fw-semibold">Order Date</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ $order->date }}" readonly>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('order') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn text-white" style="background-color: #2A9D8F;">
                            <i class="bi bi-save me-1"></i> Update Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection