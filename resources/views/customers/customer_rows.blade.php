@foreach ($customers as $customer)
<tr>
    <td>{{ $customer->name }}</td>
    <td>{{ $customer->email }}</td>
    <td>{{ $customer->emp_id }}</td>
    <td>
        <span class="badge bg-{{ $customer->role === 'admin' ? 'primary' : 'success' }}">{{ ucfirst($customer->role) }}</span>
    </td>
    <td>
        <button
            class="btn btn-sm btn-outline-dark btn-edit-customer"
            data-id="{{ $customer->id }}"
            data-name="{{ $customer->name }}"
            data-email="{{ $customer->email }}"
            data-role="{{ $customer->role }}">
            Edit
        </button>
    </td>
</tr>
@endforeach
