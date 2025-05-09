@extends('client.layout')

@section('content')
@if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md">
        {{ session('success') }}
    </div>
@endif

<h1 class="text-2xl font-semibold mb-4">Danh sách đơn hàng của bạn</h1>

<table class="table-auto w-full border border-gray-300 mb-4">
    <thead>
        <tr>
            <th class="border px-4 py-2">Mã đơn hàng</th>
            <th class="border px-4 py-2">Tổng tiền</th>
            <th class="border px-4 py-2">Trạng thái</th>
            <th class="border px-4 py-2">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td class="border px-4 py-2">{{ $order->order_code }}</td>
                <td class="border px-4 py-2">{{ number_format($order->total, 0, ',', '.') }} đ</td>
                <td class="border px-4 py-2">{{ $order->status->name }}</td>
                <td class="border px-4 py-2">
                    <a href="{{ route('order.show', $order->order_id) }}" class="text-blue-500">Xem chi tiết</a>
                    
                    @if ($order->status->status_id == 2)
                        <!-- Cho phép hủy -->
                        <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-red-500 ml-2">Hủy đơn</button>
                        </form>
                    @elseif ($order->status->status_id == 6)
                        <span class="text-gray-500 ml-2">Đã hủy</span>
                    @else
                        <span class="text-gray-400 ml-2 italic">Không thể hủy</span>
                    @endif
                    
                    @if ($order->status->status_id == 5 && !$order->refund)
                        <!-- Đã giao và chưa có yêu cầu hoàn tiền -->
                        <button id="refund-button-{{ $order->order_id }}" class="text-yellow-500 ml-2" onclick="openRefundModal({{ $order->order_id }})">Yêu cầu hoàn tiền</button>
                    @elseif ($order->refund && $order->refund->status == 'pending')
                        <!-- Đã gửi yêu cầu hoàn tiền và đang chờ xử lý -->
                        <span id="refund-status-{{ $order->order_id }}" class="text-green-500 ml-2 italic">Đã gửi yêu cầu hoàn tiền</span>
                    @elseif ($order->refund && $order->refund->status == 'rejected')
                        <!-- Yêu cầu hoàn tiền bị từ chối -->
                        <span id="refund-status-{{ $order->order_id }}" class="text-red-500 ml-2 italic">Yêu cầu hoàn tiền bị từ chối</span>
                    @elseif ($order->refund && $order->refund->status == 'approved')
                        <!-- Yêu cầu hoàn tiền đã được duyệt -->
                        <span id="refund-status-{{ $order->order_id }}" class="text-green-500 ml-2 italic">Yêu cầu hoàn tiền đã được duyệt</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Hiển thị phân trang -->
<div class="mt-4">
    {{ $orders->links() }}
</div>

<!-- Modal Yêu cầu hoàn tiền -->
<div id="refundModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-md shadow-lg w-1/3">
        <h2 class="text-xl font-semibold mb-4">Yêu cầu hoàn tiền</h2>
        <form id="refundForm" action="{{ route('order.requestRefund', ':order_id') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="order_id" id="order_id">
            
            <div class="mb-4">
                <label for="reason" class="block text-sm font-medium text-gray-700">Lý do hoàn tiền</label>
                <textarea id="reason" name="reason" rows="4" class="w-full p-2 border border-gray-300 rounded-md" required></textarea>
            </div>
    
            <div class="mb-4">
                <label for="attachments" class="block text-sm font-medium text-gray-700">Tải lên hình ảnh/video (nếu có)</label>
                <input type="file" id="attachments" name="attachments[]" accept="image/*,video/*" class="w-full p-2 border border-gray-300 rounded-md" multiple>
            </div>
    
            <div class="flex justify-end">
                <button type="submit" class="bg-green-500 text-black px-4 py-2 rounded-md">Gửi yêu cầu</button>
                <button type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md" onclick="closeRefundModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openRefundModal(orderId) {
        // Kiểm tra nếu yêu cầu hoàn tiền đã bị từ chối
        const refundStatus = document.getElementById('refund-status-' + orderId);
        if (refundStatus && refundStatus.innerText === 'Yêu cầu hoàn tiền bị từ chối') {
            alert('Yêu cầu hoàn tiền đã bị từ chối.');
            return;
        }
        
        document.getElementById('order_id').value = orderId;
        const modal = document.getElementById('refundModal');
        const form = document.getElementById('refundForm');
        
        // Thay đổi action của form
        form.action = form.action.replace(':order_id', orderId);
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
    
    function closeRefundModal() {
        const modal = document.getElementById('refundModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('#refundForm');
        if (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
    
                const orderId = document.getElementById('order_id').value;
                const refundButton = document.getElementById('refund-button-' + orderId);
    
                const formData = new FormData(this);
    
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Ẩn modal
                        closeRefundModal();
    
                        // Cập nhật UI: ẩn nút yêu cầu hoàn tiền, hiện dòng trạng thái
                        if (refundButton) refundButton.remove();
    
                        const refundStatus = document.createElement('span');
                        refundStatus.id = 'refund-status-' + orderId;
                        refundStatus.classList.add('text-green-500', 'ml-2', 'italic');
                        refundStatus.innerText = 'Đã gửi yêu cầu hoàn tiền';
    
                        const cell = refundButton?.parentElement;
                        if (cell) cell.appendChild(refundStatus);
    
                        alert("Yêu cầu hoàn tiền của bạn đã được gửi thành công!");
                    } else {
                        alert("Có lỗi xảy ra, vui lòng thử lại.");
                    }
                }).catch(error => {
                    console.error("Lỗi khi gửi yêu cầu:", error);
                    alert("Bạn đã gửi yêu cầu hoàn tiền cho đơn này rồi.");
                });
            });
        }
    });
</script>
@endsection
