@extends('layouts.app')
@section('title','Menu')
@section('content')
<div class="container-fluid px-3 mt-4">

    {{-- Alert messages container for AJAX --}}
    <div id="ajax-alert-container"></div>

    {{-- Validation Errors (only for full page reload, usually won't show with AJAX) --}}
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Create New Menu Form --}}
    <div class="card shadow-sm border-0 mb-4 mx-auto" style="max-width: 800px;">
    <div class="card-header text-center text-white" style="background-color: #f4a261; border-radius: 8px 8px 0 0;">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i> Create New Menu</h5>
        <span class="badge bg-light text-dark mt-2" id="total-menu-count">Total Menus : {{ $menuCount }}</span>
    </div>

    <div class="card-body">
        <form id="create-menu-form" action="{{ route('menus.store') }}" method="POST" novalidate>
            @csrf

            <!-- Menu Name -->
            <div class="row g-4 align-items-center mb-3">
                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">🍽️ Menu Name</label>
                    <input type="text" id="name" name="name" 
                           class="form-control shadow-sm" 
                           placeholder="Enter menu name" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-end mt-3">
                <button type="submit" class="btn text-white px-4" style="background-color: #f4a261;">
                    <i class="fas fa-plus-circle me-1"></i> Add Menu
                </button>
            </div>
        </form>
    </div>
</div>

    {{-- Menus Table --}}
    <div class="container-fluid px-3 mt-4">
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle shadow-sm rounded" style="table-layout: fixed; width: 100%;">
            <colgroup>
                <col style="width: 10%;">
                <col style="width: 50%;">
                <col style="width: 300px;">
            </colgroup>

            <thead class="table-light border-bottom">
                <tr class="text-secondary">
                    <th scope="col" class="text-center"> No </th>
                    <th scope="col" class="text-center">Menu Name</th>
                    <th scope="col" class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody id="menus-table-body">
                @forelse($menus as $index => $menu)
                <tr data-menu-id="{{ $menu->id }}">
                    <td class="ps-3 text-muted text-center row-number">{{ ($menus->currentPage() - 1) * $menus->perPage() + $index + 1 }}</td>
                    <td class="text-center menu-name">{{ $menu->name }}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-nowrap">

                            {{-- Edit Form --}}
                            <form action="{{ route('menus.update', $menu->id) }}" method="POST" class="edit-menu-form d-flex align-items-center gap-2 mb-0">
                                @csrf
                                @method('PUT')
                                <input type="text" name="name" class="form-control form-control-sm w-auto" value="{{ $menu->name }}" required>
                                <button type="submit" class="btn btn-sm"
                                    style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </form>

                            {{-- Delete Form --}}
                            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" class="mb-0"
                                onsubmit="return confirm('Are you sure to delete ID {{ $menu->id }} ({{ $menu->name }})?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm"
                                    style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                                    style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">No menu items found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    {{-- Default Laravel Pagination --}}
    @if ($menus->hasPages())
<div class="d-flex justify-content-end mt-4">
    <nav>
        <ul class="pagination pagination-sm mb-0">

            {{-- Previous Page --}}
            @if ($menus->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link rounded-pill border-0 text-muted" style="background-color: #E0E0E0;">&laquo;</span>
            </li>
            @else
            <li class="page-item">
                <a class="page-link rounded-pill border-0 text-white" href="{{ $menus->previousPageUrl() }}" style="background-color: #2A9D8F;">&laquo;</a>
            </li>
            @endif

            {{-- Page Numbers --}}
            @foreach ($menus->links()->elements[0] as $page => $url)
                @if ($page == $menus->currentPage())
                <li class="page-item active">
                    <span class="page-link rounded-pill border-0 text-white" style="background-color: #264653;">{{ $page }}</span>
                </li>
                @else
                <li class="page-item">
                    <a class="page-link rounded-pill border-0 text-dark" href="{{ $url }}" style="background-color: #E9F7F6;">{{ $page }}</a>
                </li>
                @endif
            @endforeach

            {{-- Next Page --}}
            @if ($menus->hasMorePages())
            <li class="page-item">
                <a class="page-link rounded-pill border-0 text-white" href="{{ $menus->nextPageUrl() }}" style="background-color: #2A9D8F;">&raquo;</a>
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

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alertContainer = document.getElementById('ajax-alert-container');

    function showAlert(type, message) {
        alertContainer.innerHTML = '';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.classList.remove('show');
            alertDiv.classList.add('hide');
            alertDiv.addEventListener('transitionend', () => alertDiv.remove());
        }, 3000);
    }

    // AJAX submit for CREATE form
    const createForm = document.getElementById('create-menu-form');
    const totalMenuCountBadge = document.getElementById('total-menu-count');
    const menusTableBody = document.getElementById('menus-table-body');

    createForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const url = this.action;
        const formData = new FormData(this);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': formData.get('_token'),
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.success);
                createForm.reset();

                // Add new row to table dynamically (optional: you can reload page instead)
                const newIndex = menusTableBody.querySelectorAll('tr').length + 1;
                const newRow = document.createElement('tr');
                newRow.setAttribute('data-menu-id', data.newMenu.id);
                newRow.innerHTML = `
                    <td class="ps-3 text-muted text-center row-number">${newIndex}</td>
                    <td class="text-center menu-name">${data.newMenu.name}</td>
                    <td>
                        <div class="d-flex align-items-center justify-content-center gap-2 flex-nowrap">

                            <form action="/menus/${data.newMenu.id}" method="POST" class="edit-menu-form d-flex align-items-center gap-2 mb-0">
                                <input type="hidden" name="_token" value="${formData.get('_token')}">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="text" name="name" class="form-control form-control-sm w-auto" value="${data.newMenu.name}" required>
                                <button type="submit" class="btn btn-sm"
                                    style="color: rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </form>

                            <form action="/menus/${data.newMenu.id}" method="POST" class="mb-0" onsubmit="return confirm('Are you sure to delete ID ${data.newMenu.id} (${data.newMenu.name})?');">
                                <input type="hidden" name="_token" value="${formData.get('_token')}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button class="btn btn-sm"
                                    style="color: rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                                    onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                `;
                menusTableBody.appendChild(newRow);

                // Update total menu count badge
                const currentCount = parseInt(totalMenuCountBadge.textContent.match(/\d+/)[0]);
                totalMenuCountBadge.textContent = `Total Menus : ${currentCount + 1}`;

                // Re-attach event listeners to the new edit form
                attachEditFormHandler(newRow.querySelector('.edit-menu-form'));
            } else if (data.error) {
                showAlert('danger', data.error);
            } else {
                showAlert('warning', 'Unexpected response from server.');
            }
        })
        .catch(() => {
            showAlert('danger', 'Something went wrong while adding menu.');
        });
    });

    // Attach AJAX submit handler for UPDATE forms
    function attachEditFormHandler(form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const menuId = this.closest('tr').getAttribute('data-menu-id');
            const url = this.action;
            const formData = new FormData(this);

            fetch(url, {
                method: 'POST', // Laravel expects POST + _method=PUT
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': formData.get('_token'),
                },
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const row = document.querySelector(`tr[data-menu-id="${menuId}"]`);
                    row.querySelector('.menu-name').textContent = data.updatedName;

                    showAlert('success', data.success);
                } else if (data.error) {
                    showAlert('danger', data.error);
                } else if(data.info) {
                    showAlert('info', data.info);
                } else {
                    showAlert('warning', 'Unexpected response from server.');
                }
            })
            .catch(() => {
                showAlert('danger', 'Something went wrong during update.');
            });
        });
    }

    // Attach update handler to all existing edit forms on page load
    document.querySelectorAll('.edit-menu-form').forEach(form => {
        attachEditFormHandler(form);
    });

});
</script>
@endsection
