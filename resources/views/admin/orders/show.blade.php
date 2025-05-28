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
<a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1 mb-4 text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline transition duration-150">  
    ← Quay lại danh sách đơn hàng
</a>
<div class="max-w-4xl mx-auto bg-white shadow-md rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-700 mb-4">Chi tiết đơn hàng: {{ $order->order_code }}</h1>

    <div class="mb-6 space-y-2">
        <p>
            <strong class="text-gray-600">Người đặt:</strong>
            {{ $order->user ? $order->user->username . ' - ' . $order->userAddresses->recipient_phone . ' - ' . $order->user->email : 'N/A' }}
        </p>
        <p>
            <strong class="text-gray-600">Người nhận:</strong>
            {{ $order->userAddresses ? $order->userAddresses->recipient_name . ' - ' . $order->userAddresses->recipient_phone : 'N/A' }}
        </p>
        
        <p>
            <strong class="text-gray-600">Địa chỉ nhận hàng:</strong>
            <span>
                
               @if ($order->userAddresses)
    {{ $order->userAddresses->street_address }}, {{ $order->userAddresses->ward }}, {{ $order->userAddresses->district }}, {{ $order->userAddresses->city }}<strong>({{ $order->userAddresses->address_name }})</strong> <br>
@else
    <p>Địa chỉ không tồn tại</p>
@endif

        </p>

     <p id="order-status-{{ $order->order_id }}">
    <strong class="text-gray-600">Trạng thái hiện tại:</strong>

    @php
        $lockedStatuses = [3, 4, 5, 6, 7, 8,9];
    @endphp

    @if (!in_array($order->status_id, $lockedStatuses))
        <select 
            onchange="confirmStatusChange(this, {{ $order->order_id }})"
            data-current="{{ $order->status_id }}"
            class="text-sm border rounded px-2 py-1 bg-blue-100 text-blue-800 font-medium">
            <option value="1" {{ $order->status_id == 1 ? 'selected' : '' }}>Đơn hàng mới</option>
            <option value="2" {{ $order->status_id == 2 ? 'selected' : '' }}>Đang vận chuyển</option>
            <option value="7" {{ $order->status_id == 7 ? 'selected' : '' }}>Đã giao hàng</option>
        </select>
    @else
        <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 text-sm font-medium">
            {{ $order->status->name ?? 'Chưa rõ' }}
        </span>
    @endif
</p>
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
                                    ❌ Hủy toàn bộ đơn hàng
                                </button>
                            </form>
@endif
                        @endif

    </div>
<div id="order-details-{{ $order->order_id }}">
    <h2 class="text-xl font-semibold text-gray-800 mb-3">Sản phẩm trong đơn</h2>
    <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg">
    @foreach ($order->orderDetails as $detail)
        <li class="p-4 flex items-center space-x-4">
            <!-- Hiển thị ảnh sản phẩm nếu có -->
                 @php
    $image = null;

    if ($detail->variant) {
        // Ưu tiên ảnh có variant_id trùng khớp
        $image = $detail->product->images->firstWhere('variant_id', $detail->variant->variant_id);
    }

    // Nếu không có ảnh của biến thể, dùng ảnh chính (main)
    if (!$image) {
        $image = $detail->product->images->firstWhere('type', 'main');
    }
@endphp

            @if ($detail->product->images->isNotEmpty())
                <img src="{{ asset('storage/' . ($image->image_url ?? 'default.jpg')) }}"
     alt="{{ $detail->product->name }}"
     class="w-16 h-16 object-cover rounded-md border transition-transform duration-200 hover:scale-105 hover:shadow-md">
            @else
                <span>Không có ảnh sản phẩm</span>
            @endif

            <div class="flex-1">
                <p class="text-gray-800 font-medium">{{ $detail->product->name }}</p>
                @if ($detail->variant)
                    <p class="text-sm text-gray-600 mt-1">
                        @foreach ($detail->variant->attributes as $attribute)
                            <span class="block">- {{ $attribute->variantAttribute->attribute_name }}: {{ $attribute->variantAttribute->attribute_value }}</span>
                        @endforeach
                    </p>
                @endif
                <p class="text-gray-500 text-sm">Số lượng: {{ $detail->quantity }}</p>
            </div>

            <div class="text-right text-gray-700 font-semibold">
                {{ number_format($detail->price, 0, ',', '.') }} đ
            </div>

            {{-- Nút hủy từng sản phẩm --}}
            @if ($order->status_id == 1 && (!isset($detail->status) || $detail->status != 'cancelled'))
            <input
                type="text"
                id="cancel_reason_{{ $detail->order_detail_id }}"
                placeholder="Lý do hủy"
                class="border border-gray-300 rounded px-2 py-1 text-xs ml-2"
                />
                 @if(auth()->user()->hasPermission('delete_order_detail'))
                <button
                    onclick="cancelOrderDetail({{ $order->order_id }}, {{ $detail->order_detail_id }}, this)"
                    class="cancel-product-btn ml-4 bg-red-500 hover:bg-red-600 text-white text-xs font-medium py-1 px-3 rounded-md shadow"
                >
                    ❌ Hủy sản phẩm
                </button>
                @endif

            @elseif (isset($detail->status) && $detail->status == 'cancelled')
                <span class="ml-4 text-red-600 font-semibold text-sm">Đã hủy</span>
            @endif

        </li>
    @endforeach
</ul>
</div>


    <div class="mt-6 text-right">
        <p class="text-lg font-bold text-gray-800">
            Tổng tiền đơn hàng:
             <span class="text-green-600 total-order-amount">
                {{ number_format($order->total, 0, ',', '.') }} đ
            </span>
        </p>
    </div>
    
</div>

@endsection
<script>
   document.addEventListener('DOMContentLoaded', function () {
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
            const previousValue = selectElement.getAttribute('data-current');
            selectElement.value = previousValue;
        }
    }

    window.confirmStatusChange = confirmStatusChange;

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
                if (response.status === 403) {
                    throw { error: 'Bạn không có quyền thay đổi trạng đơn hàng.' };
                }
                throw data;
            }

            showToast(data.message, 'green');

           if (parseInt(statusId) !== 1) {
                // Ẩn nút hủy toàn bộ đơn
                const cancelForm = document.getElementById(`cancel-form-${orderId}`);
                if (cancelForm) cancelForm.remove();

                // Ẩn các nút hủy từng sản phẩm
                document.querySelectorAll(`#order-details-${orderId} .cancel-product-btn`).forEach(btn => {
                    btn.style.display = 'none';
                });
            }

              if (parseInt(statusId) === 7) {
        selectElement.disabled = true;
    }
        })
        .catch(error => {
            let errorMessage = 'Có lỗi xảy ra!';
            if (error?.error) errorMessage = error.error;
            else if (error?.message) errorMessage = error.message;
            else if (typeof error === 'string') errorMessage = error;
            showToast(errorMessage, 'red');
        });
    }

    window.updateOrderStatus = updateOrderStatus;
       function cancelOrderDetail(orderId, detailId, btn) {
    const reasonInput = document.getElementById(`cancel_reason_${detailId}`);
    const cancelReason = reasonInput ? reasonInput.value.trim() : '';

    if (!cancelReason) {
        alert('Vui lòng nhập lý do hủy sản phẩm.');
        return;
    }

    if (!confirm('Bạn có chắc chắn muốn hủy sản phẩm này trong đơn hàng?')) {
        return;
    }

    btn.disabled = true;
    btn.textContent = 'Đang hủy...';

    fetch(`/admin/orders/${orderId}/details/${detailId}/cancel`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ cancel_reason: cancelReason })  // gửi lý do hủy
    })
    .then(async response => {
        const data = await response.json();

        if (!response.ok) {
            throw data;
        }

        showToast(data.message, 'green');

        btn.textContent = 'Đã hủy';
        btn.classList.remove('bg-red-500', 'hover:bg-red-600');
        btn.classList.add('bg-gray-400', 'cursor-not-allowed');
        btn.disabled = true;

        if (data.total !== undefined) {
            const totalEl = document.querySelector('.total-order-amount');
            if (totalEl) {
                totalEl.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.total);
            }
        }
    })
    .catch(error => {
        let errorMessage = 'Có lỗi xảy ra khi hủy sản phẩm!';
        if (error?.error) errorMessage = error.error;
        else if (error?.message) errorMessage = error.message;
        else if (typeof error === 'string') errorMessage = error;
        showToast(errorMessage, 'red');
        btn.disabled = false;
        btn.textContent = '❌ Hủy sản phẩm';
    });
}

    window.cancelOrderDetail = cancelOrderDetail;

});
function handleCancelSubmit(event, form) {
        event.preventDefault();

        const reason = prompt("Vui lòng nhập lý do hủy đơn hàng:");
        if (reason === null || reason.trim() === '') {
            alert("Bạn cần nhập lý do để tiếp tục.");
            return false;
        }

        const input = form.querySelector('.cancel-reason-input');
        input.value = reason;

        form.submit();
    }
</script>
