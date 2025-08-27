@foreach($menus as $menu)
<tr>
    <td>{{ $menu->id }}</td>
    <td>{{ $menu->title }}</td>
    <td>{{ $menu->description }}</td>
    <td>{{ $menu->price }}</td>
    <td>{{ $menu->category }}</td>
    <td>{{ $menu->created_at->format('Y-m-d') }}</td>
    <td>
        <!-- Action buttons (edit/delete) -->
        <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-sm btn-primary">Edit</a>
        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>
@endforeach

@if($menus->isEmpty())
<tr>
    <td colspan="7" class="text-center">No menus found.</td>
</tr>
@endif
