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
.review-modal {
    position: fixed;
    top: 50%; /* Căn giữa theo chiều dọc */
    left: 50%; /* Căn giữa theo chiều ngang */
    transform: translate(-50%, -50%); /* Điều chỉnh lại vị trí để chính giữa hoàn toàn */
    justify-content: center;
    align-items: center;

    z-index: 1000;
    display: none; /* Ẩn mặc định */
    width: 90%; /* Mở rộng chiều rộng modal, 90% màn hình */
    max-width: 800px; /* Giới hạn chiều rộng tối đa của modal */
}


.review-modal.show {
    display: flex; /* Hiện modal khi có class 'show' */
}


.media-preview {
    display: flex;
    flex-wrap: wrap; /* Cho phép xuống dòng nếu quá rộng */
    gap: 10px; /* Khoảng cách giữa các ảnh/video */
    margin-top: 10px;
}

.media-preview img,
.media-preview video {
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ccc;
}


.review-modal .bg-white {
    width: 70%;
    max-height: 90vh;
    overflow-y: auto;
    border-radius: 8px;
    padding: 20px;
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

.star-rating {
  direction: rtl; /* Đảo chiều để dễ chọn từ phải sang trái */
  font-size: 1.5rem;
  unicode-bidi: bidi-override;
  display: inline-flex;
}

.star-rating input[type="radio"] {
  display: none; /* Ẩn input radio */
}

.star-rating label {
  color: #ccc;
  cursor: pointer;
  padding: 0 2px;
  transition: color 0.2s;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input[type="radio"]:checked ~ label {
  color: #ffc107; /* màu vàng sao được chọn */
}


</style>
@if(session('success'))
    <div id="flash-message" class="bg-green-500 text-white p-4 mb-4 rounded-md">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div id="flash-message" class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('error') }}
    </div>
@endif

@if(session('info'))
    <div id="flash-message" class="bg-blue-500 text-white p-4 mb-4 rounded-md">
        {{ session('info') }}
    </div>
@endif

<script>
    setTimeout(() => {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            flashMessage.remove();
        }
    }, 5000); // 5 giây
</script>


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
              @php
    $isCancelled = $order->status_id == 5;

    $allDetails = $order->orderDetails ?? collect();
    $validDetails = $allDetails->filter(fn($detail) => $detail->status !== 'cancelled');

    $subtotal = $isCancelled ? $allDetails->sum('total_price') : $validDetails->sum('total_price');
    $discount = $order->discount_amount ?? 0;
    $shipping = $order->shipping_fee ?? 0;
    $shippingDiscount = $order->shipping_discount ?? 0;

    $finalTotal = $subtotal + $shipping - $discount - $shippingDiscount;
@endphp

<td>{{ number_format($finalTotal, 0, ',', '.') }} đ
    @if ($isCancelled)
    <p class="text-sm text-red-500 italic">Đơn hàng đã bị huỷ bởi shop — đây là tổng tiền ban đầu trước khi huỷ.</p>
@endif
</td>

                <td>{{ $order->status->name }}</td>
               <td>
    <a href="{{ route('order.show', $order->order_id) }}" class="text-blue-500">Xem chi tiết</a>

    @if($order->status->status_id == 7) 
        <form action="{{ route('orders.confirmReceived', $order->order_id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn đã nhận hàng?');">
            @csrf
            <button type="submit" class="text-green-500">Đã nhận hàng</button>
        </form>
   @elseif($order->status->status_id == 4)
    {{-- Nút trả hàng và hoàn tiền --}}
    @if (!$order->returnRequest)
        <button id="return-button-{{ $order->order_id }}" class="text-yellow-500" onclick="openReturnModal({{ $order->order_id }},{{ $order->total }})">
            Trả hàng và hoàn tiền
        </button>
    @elseif($order->returnRequest && $order->returnRequest->status == 'rejected')
        <span class="text-red-500">Yêu cầu hoàn hàng bị từ chối, vui lòng liên hệ Admin</span>
    @else
        <span class="text-orange-500 italic">Đã gửi yêu cầu trả hàng và hoàn tiền</span>
    @endif

    {{-- Nút đánh giá đơn hàng --}}
    @if(!$order->reviewed)
       <button class="text-blue-500 ml-2" onclick="if (confirmReview()) { openReviewModal({{ $order->order_id }}); }">
    Đánh giá đơn hàng
</button>

<!-- Modal đánh giá riêng cho từng đơn hàng -->
<form id="review-modal-{{ $order->order_id }}" class="review-modal" method="POST" action="{{ route('orders.review.submit') }}" enctype="multipart/form-data">
    @csrf
    <div class="bg-white w-2/3 p-6 rounded shadow-lg overflow-y-auto max-h-[80vh]">
        <input type="hidden" name="order_id" value="{{ $order->order_id }}">

        @php
            $hasActiveUnreviewed = false;
        @endphp

        @foreach ($order->orderDetails as $orderDetail)
            @php
                $isActive = $orderDetail->status === 'active';
                $isUnreviewed = !$orderDetail->reviews()->exists();
                $isReviewable = $isActive && $isUnreviewed;

                if ($isReviewable) $hasActiveUnreviewed = true;
            @endphp

            <div class="mb-6 border-b pb-4">
                <h3 class="font-semibold mb-2">
                    {{ $orderDetail->product->name }}

                    @if ($orderDetail->variant && $orderDetail->variant->variantAttributeValues)
                        <div class="mt-1">
                            @foreach ($orderDetail->variant->variantAttributeValues as $attrValue)
                                @php
                                    $attribute = $attrValue->variantAttribute;
                                @endphp
                                @if ($attribute)
                                    <span class="badge bg-secondary me-1">
                                        {{ $attribute->attribute_name }}: {{ $attribute->attribute_value }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if ($orderDetail->status === 'cancelled' || $orderDetail->status === 'canceled')
                        <span class="text-red-600 font-semibold ml-2">(Đã bị hủy)</span>
                    @endif

                    @if (!$isUnreviewed)
                        <span class="text-green-600 font-semibold ml-2">(Đã đánh giá)</span>
                    @endif
                </h3>

                @if ($isReviewable)
                    @php
                        $reviewErrors = session('review_errors')[$orderDetail->order_detail_id] ?? [];
                    @endphp

                    @if (!empty($reviewErrors))
                        <ul class="text-red-500 text-sm mb-2 list-disc list-inside">
                            @foreach ($reviewErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <label class="block mb-2">Số sao:</label>
                    <div class="star-rating mb-3">
                        @for ($i = 5; $i >= 1; $i--)
                            <input type="radio" id="star-{{ $orderDetail->order_detail_id }}-{{ $i }}" name="ratings[{{ $orderDetail->order_detail_id }}]" value="{{ $i }}">
                            <label for="star-{{ $orderDetail->order_detail_id }}-{{ $i }}" title="{{ $i }} sao">&#9733;</label>
                        @endfor
                    </div>

                    <label class="block mb-2">Nội dung đánh giá:</label>
                    <textarea name="comments[{{ $orderDetail->order_detail_id }}]" class="w-full border rounded mb-4" rows="4"></textarea>

                    <label class="block mb-2">Hình ảnh/Video (tuỳ chọn):</label>
                    <input type="file" name="media[{{ $orderDetail->order_detail_id }}][]" multiple accept="image/*,video/*" class="mb-4" onchange="previewMedia(event, {{ $orderDetail->order_detail_id }})">
                    <div class="media-preview" id="media-preview-{{ $orderDetail->order_detail_id }}"></div>
                @endif
            </div>
        @endforeach

        @unless ($hasActiveUnreviewed)
            <p class="text-center text-gray-500">Không có sản phẩm nào để đánh giá.</p>
        @endunless

        <div class="flex justify-end mt-4">
            <button type="button" onclick="closeReviewModal({{ $order->order_id }})" class="mr-2 bg-gray-500 text-white px-4 py-2 rounded">Hủy</button>
            @if ($hasActiveUnreviewed)
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Gửi đánh giá</button>
            @endif
        </div>
    </div>
</form>






    @else
        <span class="text-green-500 italic ml-2">Đã đánh giá</span>
    @endif

@elseif($order->status->status_id == 6)
    <span class="text-green-600 font-semibold italic">Đơn đã hoàn thành</span>

    @elseif($order->status->status_id == 8) 
        <span class="text-gray-500">Đơn trả hàng, hoàn tiền</span>
        @elseif($order->status->status_id == 9)
    <form action="{{ route('payment.vnpay.redirect') }}" method="GET">
        <input type="hidden" name="order_id" value="{{ $order->order_id }}">
        <input type="hidden" name="method" value="{{ $order->payment_method }}">
        <button type="submit" class="text-blue-600">Thanh toán ngay</button>
    </form>


 <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirmCancel(this);">
    @csrf
    @method('PATCH')
    <input type="hidden" name="cancel_reason" class="cancel-reason">
    <button type="submit" class="text-red-500 ml-2">Hủy đơn</button>
</form>
    @elseif($order->status->status_id == 1)
       <form action="{{ route('order.cancel', $order->order_id) }}" method="POST" onsubmit="return confirmCancel(this);">
    @csrf
    @method('PATCH')
    <input type="hidden" name="cancel_reason" class="cancel-reason">
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
function confirmReview() {
    return confirm("Sau khi đánh giá, bạn sẽ không thể trả hàng nữa. Bạn có chắc muốn tiếp tục?");
}
function openReviewModal(orderId) {
    const modal = document.getElementById(`review-modal-${orderId}`);
    if (modal) {
        modal.style.display = 'block';
        document.body.classList.add('overflow-hidden');

    }
}

function closeReviewModal(orderId) {
    const modal = document.getElementById(`review-modal-${orderId}`);
    if (modal) {
        modal.classList.remove('show'); // Loại bỏ class 'show' để ẩn modal
        modal.style.display = 'none';   // Đảm bảo ẩn modal bằng cách thay đổi display
    }
}

    document.addEventListener('DOMContentLoaded', function () {
        const reviewErrors = @json(session('review_errors'));

        if (reviewErrors) {
            // Duyệt qua các order_detail_id có lỗi
            for (const orderDetailId in reviewErrors) {
                // Tìm form cha (modal) chứa lỗi đó
                const modalForm = document.querySelector(`form.review-modal`);
                
                if (modalForm) {
                    // Mở lại modal đó (tuỳ bạn dùng kiểu modal nào, ví dụ Tailwind Modal, AlpineJS, hoặc custom)
                    modalForm.style.display = 'block'; // Giả sử bạn dùng display: none/block để ẩn/hiện modal
                    document.body.classList.add('overflow-hidden'); // Nếu cần scroll lock
                    break; // Mở một modal là đủ
                }
            }
        }
    });
    function previewMedia(event, orderDetailId) {
        const mediaPreviewContainer = document.getElementById('media-preview-' + orderDetailId);
        mediaPreviewContainer.innerHTML = ''; // Clear previous previews

        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const fileReader = new FileReader();

            fileReader.onload = function(e) {
                const fileUrl = e.target.result;
                const fileType = file.type.split('/')[0];

                // Create preview element
                let previewElement;
                if (fileType === 'image') {
                    previewElement = document.createElement('img');
                    previewElement.src = fileUrl;
                    previewElement.style.width = '100px';
                    previewElement.style.height = '100px';
                    previewElement.style.objectFit = 'cover';
                    previewElement.classList.add('rounded');
                } else if (fileType === 'video') {
                    previewElement = document.createElement('video');
                    previewElement.src = fileUrl;
                    previewElement.width = 100;
                    previewElement.classList.add('rounded');
                    previewElement.setAttribute('controls', 'true');
                }

                // Append preview element
                if (previewElement) {
                    mediaPreviewContainer.appendChild(previewElement);
                }
            };

            fileReader.readAsDataURL(file);
        }
      
    }
      function confirmCancel(form) {
        const reason = prompt("Vui lòng nhập lý do hủy đơn:");
        if (reason === null || reason.trim() === "") {
            alert("Bạn cần nhập lý do hủy đơn.");
            return false;
        }
        form.querySelector('.cancel-reason').value = reason;
        return true;
    }
</script>


@endsection
