@extends('client.layout')

@section('content')
<style>
    /* Flash message */
#flash-message, #flash-error {
    transition: opacity 0.5s ease-in-out;
}

#flash-message {
    background-color: #d4edda; /* Light green */
    color: #155724; /* Dark green */
}

#flash-error {
    background-color: #f8d7da; /* Light red */
    color: #721c24; /* Dark red */
}

/* Table Styling */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}

th, td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
}

th {
    background-color: #f3f4f6;
    font-weight: bold;
    color: #333;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #e9ecef;
}

/* Button Styling */
button {
    padding: 8px 16px;
    border-radius: 4px;
    transition: all 0.3s ease;
}

button:hover {
    opacity: 0.8;
}

.text-blue-500 {
    color: #3b82f6;
}

.text-yellow-500 {
    color: #f59e0b;
}

.text-red-500 {
    color: #ef4444;
}

.text-green-500 {
    color: #10b981;
}

.bg-blue-500 {
    background-color: #3b82f6;
}

.bg-yellow-500 {
    background-color: #f59e0b;
}

.bg-red-500 {
    background-color: #ef4444;
}

.bg-gray-500 {
    background-color: #6b7280;
}

#returnModal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;  /* Sử dụng flex để căn giữa modal */
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.5);  /* Màu nền đen mờ */
    z-index: 1000;
    opacity: 0;  /* Ẩn modal bằng cách giảm độ mờ */
    pointer-events: none; /* Không cho phép tương tác với modal khi nó bị ẩn */
    transition: opacity 0.3s ease; /* Thêm hiệu ứng mờ dần */
}

#returnModal.show {
    opacity: 1; /* Hiển thị modal */
    pointer-events: auto; /* Cho phép tương tác với modal khi nó hiển thị */
}

#returnModal .bg-white {
    width: 40%;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}



textarea {
    width: 100%;
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    margin-bottom: 1rem;
}

input[type="file"] {
    border-radius: 4px;
    padding: 8px;
    border: 1px solid #ccc;
    width: 100%;
}

button[type="submit"] {
    background-color: #f59e0b;
    color: white;
}

button[type="button"] {
    background-color: #6b7280;
    color: white;
}

button[type="button"]:hover {
    background-color: #4b5563;
}

button[type="submit"]:hover {
    background-color: #d97706;
}

/* Pagination Styling */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
}

.pagination .page-link {
    padding: 10px 20px;
    margin: 0 5px;
    background-color: #f3f4f6;
    color: #333;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.pagination .page-link:hover {
    background-color: #ddd;
    color: #333;
}

.pagination .page-item.active .page-link {
    background-color: #3b82f6;
    color: white;
}

</style>
@if(session('success'))

     <div id="flash-error" class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('success') }}
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

@if(session('info'))
    <div id="flash-error" class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('info') }}
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

<h1 class="text-2xl font-semibold mb-4">Danh sách đơn hàng của bạn</h1>

<table>
    <thead>
        <tr>
            <th>Mã đơn hàng</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_code }}</td>
                <td>{{ number_format($order->total, 0, ',', '.') }} đ</td>
                <td>{{ $order->status->name }}</td>
               <td>
    <a href="{{ route('order.show', $order->order_id) }}" class="text-blue-500">Xem chi tiết</a>

    @if($order->status->status_id == 7) 
        <form action="{{ route('orders.confirmReceived', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận hàng?');">
            @csrf
            <button type="submit" class="text-green-500">Đã nhận hàng</button>
        </form>
    @elseif($order->status->status_id == 4) 
        @if (!$order->returnRequest)
            <button id="return-button-{{ $order->order_id }}" class="text-yellow-500" onclick="openReturnModal({{ $order->order_id }},{{ $order->total }})">Trả hàng và hoàn tiền</button>
        @elseif($order->returnRequest && $order->returnRequest->status == 'rejected')
            <span class="text-red-500">Yêu cầu hoàn hàng bị từ chối, vui lòng liên hệ Admin</span>
        @else
            <span class="text-orange-500 italic">Đã gửi yêu cầu trả hàng và hoàn tiền</span>
        @endif
    @elseif($order->status->status_id == 8) 
        <span class="text-gray-500">Đơn trả hàng, hoàn tiền</span>
    @elseif($order->status->status_id == 1)
        <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
            @csrf
            @method('PATCH')
            <button type="submit" class="text-red-500 ml-2">Hủy đơn</button>
        </form>
    @else
        <span class="text-gray-400 ml-2 italic">Không thể hủy</span>
    @endif
</td>

            </tr>
        @endforeach
    </tbody>
</table>

<!-- Modal Trả hàng và hoàn tiền -->
<div id="returnModal" class="hidden">
    <div class="bg-white p-6 rounded-md shadow-lg">
        <h2 class="text-xl font-semibold mb-4">Yêu cầu trả hàng và hoàn tiền</h2>
        <form action="{{ route('order.returnRequest') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="order_id" id="return_order_id">
            <input type="hidden" name="amount" id="return_amount">

            <div class="mb-4">
                <label for="return_reason" class="block text-sm font-medium">Lý do trả hàng</label>
                <textarea id="return_reason" name="reason" rows="4" required></textarea>
            </div>

            <div class="mb-4">
                <label for="return_attachments" class="block text-sm font-medium">Tệp hình ảnh/video liên quan</label>
                <input type="file" id="return_attachments" name="attachments[]" accept="image/*,video/*" multiple>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-yellow-500 text-black">Gửi yêu cầu</button>
                <button type="button" class="ml-2 bg-gray-500 text-white" onclick="closeReturnModal()">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Pagination -->
<div class="pagination mt-4">
    {{ $orders->links() }}
</div>

<script>
function openReturnModal(orderId, amount) {
    document.getElementById('return_order_id').value = orderId;
    document.getElementById('return_amount').value = amount;
    
    // Thêm lớp 'show' để modal hiển thị
    document.getElementById('returnModal').classList.add('show');
}

function closeReturnModal() {
    // Xóa lớp 'show' để ẩn modal
    document.getElementById('returnModal').classList.remove('show');
}

// Đảm bảo modal có thể đóng khi bấm ngoài vùng modal
document.getElementById('returnModal').addEventListener('click', function(event) {
    // Chỉ đóng modal khi bấm vào vùng ngoài modal
    if (event.target === this) {
        closeReturnModal();
    }
});

</script>

@endsection
