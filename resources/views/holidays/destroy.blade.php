@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Delete Customer</h2>

    <p>Are you sure you want to delete customer <strong>{{ $customer->name }}</strong> (ID: {{ $customer->emp_id }})?</p>

    <form action="{{ route('customers.destroy', $customer->emp_id) }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
