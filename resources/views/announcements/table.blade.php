<table class="table table-hover align-middle mb-0">
    <thead class="table-light text-uppercase">
        <tr>
            <th style="width: 20%;">Date</th>
            <th style="width: 20%;">Title</th>
            <th>Text</th>
            <th style="width: 18%;">Posted At</th>
            <th style="width: 18%;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($announcements as $announcement)
        <tr>
            <td>{{ \Carbon\Carbon::parse($announcement->date)->format('F j, Y') }}</td>
            <td>{{ \Illuminate\Support\Str::limit($announcement->title, 40) }}</td>
            <td>{{ \Illuminate\Support\Str::limit($announcement->text, 60) }}</td>
            <td>{{ $announcement->created_at->format('Y-m-d H:i') }}</td>
            <td class="text-nowrap">
                <a href="{{ route('announcements.show', $announcement->id) }}"
                    class="btn btn-sm"
                    style="color:rgb(0, 133, 117); border: 1px solid rgb(0, 133, 117); background-color: transparent;"
                    onmouseover="this.style.backgroundColor='rgb(0, 133, 117)'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(0, 133, 117)';"
                    title="View Announcement">
                    <i class="far fa-eye"></i>
                </a>
                <a href="{{ route('announcements.edit', $announcement->id) }}"
                    class="btn btn-sm"
                    style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                    onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';"
                    title="Edit Announcement">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('announcements.destroy', $announcement->id) }}" method="POST"
                    class="d-inline-block"
                    onsubmit="return confirm('Are you sure you want to delete this announcement ({{$announcement->title}}) on {{$announcement->date}}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm"
                        style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                        onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                        onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';"
                        title="Delete Announcement">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center text-muted py-4">No announcements found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

@if ($announcements->hasPages())
<div class="d-flex justify-content-end mt-4">
    <nav>
        <ul class="pagination pagination-sm mb-0">
            @if ($announcements->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link rounded-pill border-0 text-white" href="{{ $announcements->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
            </li>
            @endif

            @foreach ($announcements->links()->elements[0] as $page => $url)
                @if ($page == $announcements->currentPage())
                <li class="page-item active">
                    <span class="page-link rounded-pill border-0 text-white" style="background-color: #264653;">{{ $page }}</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-dark" href="{{ $url }}" style="background-color: #E9F7F6;">{{ $page }}</a>
                </li>
                @endif
            @endforeach

            @if ($announcements->hasMorePages())
            <li class="page-item">
                <a class="page-link rounded-pill border-0 text-white" href="{{ $announcements->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
            </li>
            @else
            <li class="page-item disabled">
                <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&raquo;</span>
            </li>
            @endif
        </ul>
    </nav>
</div>
@endif
