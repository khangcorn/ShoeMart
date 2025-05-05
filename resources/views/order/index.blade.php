@extends('client.layout')

@section('content')
@if(session('success'))
    <div class="bg-green-100 text-green-800 p-2 rounded mb-4">
        {{ session('success') }}
    </div>
@endif

@if(session('info'))
    <div class="bg-yellow-100 text-yellow-800 p-2 rounded mb-4">
        {{ session('info') }}
    </div>
@endif

@if(session('error'))
    <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
        {{ session('error') }}
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

                        @if($order->status->status_id == 7) 
                            <!-- Nút "Đã nhận hàng" chỉ hiển thị khi đơn ở trạng thái "Đã giao hàng" -->
                            <form action="{{ route('orders.confirmReceived', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận hàng?');">
                                @csrf
                                <button type="submit" class="text-green-500">Đã nhận hàng</button>
                            </form>
                        @elseif($order->status->status_id == 4) 
                            <!-- Nút "Hoàn hàng" chỉ hiển thị khi đơn ở trạng thái "Đã nhận hàng" -->
                            @if (!$order->returnRequest)
                                <button id="return-button-{{ $order->order_id }}" class="text-yellow-500" onclick="openReturnModal({{ $order->order_id }},{{ $order->total }})">Trả hàng và hoàn tiền</button>
                            @else
                                @if (strtolower($order->returnRequest->status) === 'rejected')
                                    <span class="text-gray-500">Yêu cầu hoàn hàng bị từ chối, vui lòng liên hệ admin</span>
                                @else
                                    <span class="text-orange-500 italic">Đã gửi yêu cầu trả hàng và hoàn tiền</span>
                            @endif
                        @endif
                        @elseif($order->status->status_id == 8) 
                        <!-- Nút "Hoàn tiền" khi đơn ở trạng thái "Đơn trả hàng, hoàn tiền" -->
                        @if($order->returnRequest && strtolower($order->returnRequest->status) === 'approved')
                            <span class="text-gray-500">Trả hàng thành công, tiền đã cộng vào ví</span>
                        @else
                            <span class="text-gray-500">Đơn trả hàng, hoàn tiền</span>
                        @endif
                    
                    

                        
                        @elseif($order->status->status_id == 1)
                            <!-- Chỉ hiển thị nút "Hủy đơn" khi trạng thái là 'Đơn hàng mới' -->
                            <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-red-500">Hủy đơn</button>
                            </form>
                        
                        @elseif ($order->status->status_id == 3)
                            <span class="text-gray-500 ml-2">Đã hủy bởi bạn</span>
                        @elseif ($order->status->status_id == 5)
                            <span class="text-gray-500 ml-2">Đã hủy bởi shop, vui lòng liên hệ admin để biết thêm</span>
                        @else
                            <span class="text-gray-400 ml-2 italic">Không thể hủy</span>
                        @endif
                        
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
   <!-- Modal Trả hàng và hoàn tiền -->
   <div id="returnModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
    <div class="bg-white p-6 rounded-md shadow-lg w-1/3">
        <h2 class="text-xl font-semibold mb-4">Yêu cầu trả hàng và hoàn tiền</h2>
        <form action="{{ route('order.returnRequest') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH') <!-- Sử dụng phương thức PATCH -->
            <input type="hidden" name="order_id" id="return_order_id"> <!-- Cập nhật order_id -->
            <input type="hidden" name="amount" id="return_amount"> <!-- Cập nhật amount -->

            <div class="mb-4">
                <label for="return_reason" class="block text-sm font-medium text-gray-700">Lý do trả hàng</label>
                <textarea id="return_reason" name="reason" rows="4" class="w-full p-2 border border-gray-300 rounded-md" required></textarea>
            </div>

            <div class="mb-4">
                <label for="return_attachments" class="block text-sm font-medium text-gray-700">Tệp hình ảnh/video liên quan</label>
                <input type="file" id="return_attachments" name="attachments[]" accept="image/*,video/*" class="w-full p-2 border border-gray-300 rounded-md" multiple>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-500 text-black px-4 py-2 rounded-md">Gửi yêu cầu</button>
                <button type="button" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md" onclick="closeReturnModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>



    </table>

    <!-- Hiển thị phân trang -->
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    

    <script>
function openReturnModal(orderId, amount) {
    document.getElementById('return_order_id').value = orderId;
    document.getElementById('return_amount').value = amount; // Cập nhật amount
    const modal = document.getElementById('returnModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const returnButton = document.getElementById('return-button-' + orderId);
    if (returnButton) {
        returnButton.style.display = 'none';
    }
}

function closeReturnModal() {
    const modal = document.getElementById('returnModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}


    </script>
@endsection
