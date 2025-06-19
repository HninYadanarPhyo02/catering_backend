@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Delete Menu Item</h2>

    <p>Are you sure you want to delete the menu item: <strong>{{ $menu->name }}</strong>?</p>

    <form action="{{ route('menus.destroy', $menu->food_id) }}" method="POST">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-danger">Yes, Delete</button>
        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
