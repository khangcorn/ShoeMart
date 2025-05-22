@extends('admin.layout')

@section('content')
@if(session('success'))
        <div class=" bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded relative" id="flash-message" >
            {{ session('success') }}
    </div>

    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) {
                flash.remove();
            }
        }, 5000); 
    </script>
@endif


@if(session('error'))
    <div id="flash-error" class="bg-red-500 text-white px-4 py-3  rounded-md">
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

<div class="px-4 py-4">



        <table class="w-full ">
            <thead>
                <tr >
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Mã đơn</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Người đặt</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">SĐT</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Email</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Trạng thái</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Ngày đặt</th>
                    <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thao tác</th>
                </tr>
            </thead>
            <tbody >
                @foreach ($orders as $order)
                    <tr >
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $order->order_code }}</td>
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $order->user->username ?? 'N/A' }}</td>
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $order->user->phone ?? 'N/A' }}</td>
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $order->user->email ?? 'N/A' }}</td>
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
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
                        
                        
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $order->created_at->format('d/m/Y') }}</td>
                        <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                            <div class="flex justify-center items-center gap-2"> 
                                <a
                                class="cursor-pointer text-sm p-1.5 rounded-full bg-[#ECFDF3] text-[#03A27E] flex items-center justify-center"
                                href="{{ route('admin.orders.show', $order->order_id) }}"
                              >
                                <svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                    <g transform="translate(1, 4)" fill="#000000">
                                        <path d="M20.92,7.6 C18.9,2.91 15.1,0 11,0 C6.9,0 3.1,2.91 1.08,7.6 C0.968686852,7.85505046 0.968686852,8.14494954 1.08,8.4 C3.1,13.09 6.9,16 11,16 C15.1,16 18.9,13.09 20.92,8.4 C21.0313131,8.14494954 21.0313131,7.85505046 20.92,7.6 Z M11,14 C7.83,14 4.83,11.71 3.1,8 C4.83,4.29 7.83,2 11,2 C14.17,2 17.17,4.29 18.9,8 C17.17,11.71 14.17,14 11,14 Z M11,4 C8.790861,4 7,5.790861 7,8 C7,10.209139 8.790861,12 11,12 C13.209139,12 15,10.209139 15,8 C15,6.93913404 14.5785726,5.92171839 13.8284271,5.17157288 C13.0782816,4.42142736 12.060866,4 11,4 Z M11,10 C9.8954305,10 9,9.1045695 9,8 C9,6.8954305 9.8954305,6 11,6 C12.1045695,6 13,6.8954305 13,8 C13,9.1045695 12.1045695,10 11,10 Z" />
                                      </g>
                                </svg>
                              </a>
                              @if ($order->status_id == 1) 
                              <form id="cancel-form-{{ $order->order_id }}" action="{{ route('admin.orders.cancel', $order->order_id) }}" method="POST" class="inline-block ml-2">
                                  @csrf
                                  @method('PUT')
                                  <button
                                      type="submit"
                                      class="cursor-pointer text-sm p-1.5 rounded-full bg-[#FEF3F2] text-[#D93948] flex items-center justify-center"
                                    >
                                      <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <g id="SVGRepo_bgCarrier" stroke-width="1"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 6H20M16 6L15.7294 5.18807C15.4671 4.40125 15.3359 4.00784 15.0927 3.71698C14.8779 3.46013 14.6021 3.26132 14.2905 3.13878C13.9376 3 13.523 3 12.6936 3H11.3064C10.477 3 10.0624 3 9.70951 3.13878C9.39792 3.26132 9.12208 3.46013 8.90729 3.71698C8.66405 4.00784 8.53292 4.40125 8.27064 5.18807L8 6M18 6V16.2C18 17.8802 18 18.7202 17.673 19.362C17.3854 19.9265 16.9265 20.3854 16.362 20.673C15.7202 21 14.8802 21 13.2 21H10.8C9.11984 21 8.27976 21 7.63803 20.673C7.07354 20.3854 6.6146 19.9265 6.32698 19.362C6 18.7202 6 17.8802 6 16.2V6M14 10V17M10 10V17" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g>
                                      </svg>
                                    </button>
                              </form>
                          @endif
                            </div>
                         
                        
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>


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


</script>

@endsection
