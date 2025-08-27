@forelse ($orders as $order)
<tr class="text-center">
    <td class="fw-semibold text-start">{{ $order->food_name }}</td>
    <td>{{ number_format($order->price) }}</td>
    <td>{{ \Carbon\Carbon::parse($order->date)->format('Y-m-d') }}</td>
    <td>
        <!-- Edit button -->
        <button type="button" class="btn btn-sm btn-edit-order"
                data-id="{{ $order->id }}"
                data-food_id="{{ $order->food_id }}"
                data-date="{{ $order->date }}"
                data-price="{{ $order->price }}"
                data-bs-toggle="modal"
                data-bs-target="#editOrderModal"
                style="color:rgb(230, 165, 3); border: 1px solid rgb(230, 165, 3); background-color: transparent;"
                onmouseover="this.style.backgroundColor='rgb(230, 165, 3)'; this.style.color='white';"
                onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(230, 165, 3)';">
            <i class="fas fa-edit"></i>
        </button>

        <!-- Delete form -->
        <form action="{{ route('orders.destroyByDate', ['date' => \Carbon\Carbon::parse($order->date)->format('Y-m-d')]) }}"
              method="POST" class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete all orders on {{$order->date}} ?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm"
                    style="color:rgb(182, 48, 14); border: 1px solid rgb(182, 48, 14); background-color: transparent;"
                    onmouseover="this.style.backgroundColor='rgb(182, 48, 14)'; this.style.color='white';"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.color='rgb(182, 48, 14)';">
                <i class="fas fa-trash-alt"></i>
            </button>
        </form>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="text-center text-muted">No orders found.</td>
</tr>
@endforelse
