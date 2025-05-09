@extends('admin.layout')

@section('content')
@if(session('success'))
    <div id="flash-message" class="fixed top-5 right-5 bg-green-100 text-green-800 px-4 py-2 rounded shadow z-50">
        {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.remove();
            }
        }, 5000); // 5000ms = 5 giây
    </script>
@endif


@if(session('error'))
    <div id="flash-error" class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('error') }}
    </div>

    <script>
        setTimeout(() => {
            const flashError = document.getElementById('flash-error');
            if (flashError) {
                flashError.remove();
            }
        }, 5000); // 5 giây
    </script>
@endif
<div id="toast" class="hidden"></div>

<div class="max-w-7xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-700">Danh sách đơn hàng</h1>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr class="border-b border-gray-300">
                    <th class="px-4 py-3 border">Mã đơn</th>
                    <th class="px-4 py-3 border">Người đặt</th>
                    <th class="px-4 py-3 border">SĐT</th>
                    <th class="px-4 py-3 border">Email</th>
                    <th class="px-4 py-3 border">Trạng thái</th>
                    <th class="px-4 py-3 border">Ngày đặt</th>
                    <th class="px-4 py-3 border">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $order->order_code }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->username ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->phone ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->email ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            @php
                                $lockedStatuses = [3,4,5,6,7,8];
                            @endphp
                            @if (!in_array($order->status_id, $lockedStatuses))
                                <select onchange="updateOrderStatus(this, {{ $order->order_id }})"
                                        class="text-sm border rounded px-2 py-1 bg-white">
                                    <option value="1" {{ $order->status_id == 1 ? 'selected' : '' }}>Đơn hàng mới</option>
                                    <option value="2" {{ $order->status_id == 2 ? 'selected' : '' }}>Đang vận chuyển</option>
                                    <option value="7" {{ $order->status_id == 7 ? 'selected' : '' }}>Đã giao hàng</option>
                                </select>
                            @else
                                {{ $order->status->name ?? 'Chưa rõ' }}
                            @endif
                        </td>
                        
                        
                        <td class="px-4 py-3 text-gray-700">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.orders.show', $order->order_id) }}"
                               class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md shadow">
                                👁️ Xem
                            </a>
                            @if ($order->status_id == 1) 
                            <form id="cancel-form-{{ $order->order_id }}" action="{{ route('admin.orders.cancel', $order->order_id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1.5 px-3 rounded-md shadow">
                                    ❌ Hủy
                                </button>
                            </form>
                        @endif
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $orders->links('pagination::tailwind') }}
    </div>
</div>
<script>
    function showToast(message, color = 'green') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `fixed top-5 right-5 z-50 px-4 py-2 rounded shadow-lg text-white text-sm bg-${color}-500`;
        toast.classList.remove('hidden');

        setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }
function updateOrderStatus(selectElement, orderId) {
    const statusId = selectElement.value;

    fetch(`/admin/orders/${orderId}/ajax-update-status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status_id: statusId })
    })
    .then(async (response) => {
        const data = await response.json();
        if (!response.ok) {
            throw data;
        }
        showToast(data.message, 'green');
          // Nếu trạng thái mới không còn là "Đơn hàng mới", ẩn nút Hủy
          if (parseInt(statusId) !== 1) {
            const cancelForm = document.getElementById(`cancel-form-${orderId}`);
            if (cancelForm) {
                cancelForm.remove();
            }
        }
        
        // Nếu trạng thái mới là "Đã giao hàng" (id = 7), thay thế select bằng text
        if (parseInt(statusId) === 7) {
            const parent = selectElement.parentElement;
            parent.innerHTML = 'Đã giao hàng'; // hoặc data.new_status_name nếu bạn trả về từ server
        }
    })
    .catch(error => {
        // Nếu Laravel trả về lỗi xác thực hoặc lỗi server
        let errorMessage = 'Có lỗi xảy ra!';
        if (error?.error) {
            errorMessage = error.error;
        } else if (error?.message) {
            errorMessage = error.message;
        } else if (typeof error === 'string') {
            errorMessage = error;
        }
        showToast(errorMessage, 'red');
    });
}

</script>

@endsection
