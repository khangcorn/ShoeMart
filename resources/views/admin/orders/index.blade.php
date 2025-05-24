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
    <div class="mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center justify-between gap-4">
        {{-- Bên trái: Tìm theo mã đơn và nút lọc --}}
        <div class="flex items-center gap-4">
            <input type="text" name="order_code" value="{{ request('order_code') }}"
                   placeholder="Nhập mã đơn hàng"
                   class="border rounded px-3 py-2 w-48 text-sm" />

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm shadow">
                🔍 Tìm kiếm
            </button>
        </div>

        {{-- Bên phải: Bộ lọc thời gian --}}
        <div>
            <select name="date_filter" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
                <option value="3" {{ request('date_filter', '3') == '3' ? 'selected' : '' }}>3 ngày gần đây</option>
                <option value="7" {{ request('date_filter') == '7' ? 'selected' : '' }}>7 ngày gần đây</option>
                <option value="30" {{ request('date_filter') == '30' ? 'selected' : '' }}>30 ngày gần đây</option>
                <option value="all" {{ request('date_filter') == 'all' ? 'selected' : '' }}>Tất cả</option>
            </select>
        </div>
    </form>
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
                        <td class="px-4 py-3 text-gray-700">
                            <a  class="text-blue-600 hover:underline" >
                                {{ $order->order_code }}
                            </a>
                        </td>

                        <td class="px-4 py-3 text-gray-700">{{ $order->user->username ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->phone ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->user->email ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">
                            @php
                                $lockedStatuses = [3,4,5,6,7,8,9];
                            @endphp
                            @if (!in_array($order->status_id, $lockedStatuses))
                             @if(auth()->user()->hasPermission('update_order_status'))
                              <select 
                                onchange="confirmStatusChange(this, {{ $order->order_id }})"
                                data-current="{{ $order->status_id }}"
                                class="text-sm border rounded px-2 py-1 bg-white">
                                <option value="1" {{ $order->status_id == 1 ? 'selected' : '' }}>Đơn hàng mới</option>
                                <option value="2" {{ $order->status_id == 2 ? 'selected' : '' }}>Đang vận chuyển</option>
                                <option value="7" {{ $order->status_id == 7 ? 'selected' : '' }}>Đã giao hàng</option>
                            </select>
                            @endif
                            @else
                                {{ $order->status->name ?? 'Chưa rõ' }}
                            @endif
                        </td>
                        
                        
                        <td class="px-4 py-3 text-gray-700">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                             @if(auth()->user()->hasPermission('view_order_details'))
                            <a href="{{ route('admin.orders.show', $order->order_id) }}"
                               class="inline-block bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium py-1.5 px-3 rounded-md shadow">
                                👁️ Xem
                            </a>
                            @endif
                            @if ($order->status_id == 1) 
                             @if(auth()->user()->hasPermission('delete_order'))
                            <form id="cancel-form-{{ $order->order_id }}"
                                action="{{ route('admin.orders.cancel', $order->order_id) }}"
                                method="POST"
                                class="inline-block ml-2"
                                onsubmit="return handleCancelSubmit(event, this);">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="cancel_reason" class="cancel-reason-input">
                                <button type="submit" class="inline-block bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1.5 px-3 rounded-md shadow">
                                    ❌ Hủy
                                </button>
                            </form>
                            @endif

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

    function confirmStatusChange(selectElement, orderId) {
        const selectedOption = selectElement.options[selectElement.selectedIndex].text;
        if (confirm(`Bạn có chắc muốn chuyển trạng thái đơn hàng thành "${selectedOption}"?`)) {
            updateOrderStatus(selectElement, orderId);
        } else {
            // Nếu hủy, khôi phục lại lựa chọn ban đầu (trước khi thay đổi)
            const previousValue = selectElement.getAttribute('data-current');
            selectElement.value = previousValue;
        }
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

        // Kiểm tra nếu response không hợp lệ (mã lỗi khác 200)
        if (!response.ok) {
            // Kiểm tra nếu là lỗi 403 (không có quyền)
            if (response.status === 403) {
                throw { error: 'Bạn không có quyền thay đổi trạng đơn hàng.' };
            }
            // Các lỗi khác (500, 404, v.v...)
            throw data;
        }

        // Nếu thành công, hiển thị thông báo thành công
        showToast(data.message, 'green');

        // Nếu trạng thái không còn là "Đơn hàng mới", ẩn nút Hủy
        if (parseInt(statusId) !== 1) {
            const cancelForm = document.getElementById(`cancel-form-${orderId}`);
            if (cancelForm) {
                cancelForm.remove();
            }
        }

        // Nếu trạng thái mới là "Đã giao hàng" (id = 7), thay thế select bằng text
        if (parseInt(statusId) === 7) {
            const parent = selectElement.parentElement;
            parent.innerHTML = 'Đã giao hàng'; // Hoặc data.new_status_name nếu bạn trả về từ server
        }
    })
    .catch(error => {
        // Nếu Laravel trả về lỗi xác thực hoặc lỗi server
        let errorMessage = 'Có lỗi xảy ra!';
        
        if (error?.error) {
            errorMessage = error.error; // Lỗi không có quyền
        } else if (error?.message) {
            errorMessage = error.message; // Các lỗi khác từ server
        } else if (typeof error === 'string') {
            errorMessage = error; // Lỗi thông thường
        }
        
        // Hiển thị thông báo lỗi
        showToast(errorMessage, 'red');
    });
}

 function handleCancelSubmit(event, form) {
        event.preventDefault(); // Ngăn form gửi ngay

        const reason = prompt("Vui lòng nhập lý do hủy đơn:");
        if (reason === null || reason.trim() === '') {
            alert("Bạn cần nhập lý do hủy đơn.");
            return false;
        }

        // Gán lý do vào input ẩn
        const input = form.querySelector('.cancel-reason-input');
        input.value = reason;

        // Submit form
        form.submit();
    }
</script>

@endsection
