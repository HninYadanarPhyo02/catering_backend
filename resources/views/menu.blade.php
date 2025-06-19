@extends('layouts.app')
@section('title','Menu')
@section('content')
<div class="container mt-5">

    <!-- <h4 class="mb-4">Menu Management</h4> -->
     <h4>Menu Management</h4>

    {{-- Create / Update Form --}}
    @if(isset($editMenu))
        <p>it is edit</p>
    @endif
    <form action="{{ isset($editMenu) ? route('menus.update', $editMenu->id) : route('menus.store') }}" method="POST">
        <h3>Menu Management</h3>
        @csrf
        @if(isset($editMenu))
            @method('PUT')
        @endif

        <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Enter menu name"
                   value="{{ isset($editMenu) ? $editMenu->name : old('name') }}" required>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">
                {{ isset($editMenu) ? 'Update Menu' : 'Add Menu' }}
            </button>
            @if(isset($editMenu))
                <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
            @endif
        </div>
    </form>

    {{-- Display Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger mt-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Menu List --}}
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Menu Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($menus as $menu)
                <tr>
                    <td>{{ $menu->id }}</td>
                    <td>{{ $menu->name }}</td>
                    <td>
                        <a href="{{ route('menus.index', $menu->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

