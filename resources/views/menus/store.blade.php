@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Add New Menu</h2>

    {{-- Flash Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
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

    {{-- Create Menu Form --}}
    <form action="{{ route('menus.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Menu Name</label>
            <input type="text" name="name" class="form-control" placeholder="Enter menu name" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Menu</button>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Back to Menu List</a>
    </form>
</div>
@endsection
