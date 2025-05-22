@extends('admin.layout')

@section('content')
<style>
    
</style>
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
{{-- Bảng yêu cầu hoàn tiền --}}
<div class="px-4 py-4">
    <table class="w-full">
        <thead>
            <tr>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Mã đơn hàng</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Người yêu cầu</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Lý do</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Tổng tiền</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Trạng thái</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Người duyệt</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thời gian duyệt</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Tệp đính kèm</th>
                 <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($refundRequests as $refund)
                <tr>
                     <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $refund->order->order_code ?? 'N/A' }}</>
                     <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $refund->user->username ?? 'N/A' }}</>
                     <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ $refund->reason }}</>
                     <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">{{ number_format($refund->amount, 0, ',', '.') }} đ</>
                     <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        @if ($refund->status === 'approved')
                            Đã duyệt
                        @elseif ($refund->status === 'rejected')
                            Đã từ chối
                        @else
                            Chưa duyệt
                        @endif
                    </>
                    
                    
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        @if ($refund->approved_by && $refund->approvedBy)
                            {{ $refund->approvedBy->username }}
                        @else
                            <span class="text-gray-500">Admin</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        @if ($refund->approved_at)
                            {{ \Carbon\Carbon::parse($refund->approved_at)->format('d/m/Y H:i') }}
                        @else
                            <span class="text-gray-500">Chưa duyệt</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        @if($refund->attachments)
                                @foreach(json_decode($refund->attachments) as $attachment)
                                @php
                                    $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                                    $url = asset('storage/' . $attachment);
                                @endphp
                            
                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                    <a href="{{ $url }}" target="_blank">
                                        <img src="{{ $url }}" alt="Attachment" width="50" class="inline-block mr-2">
                                    </a>
                                @elseif (in_array($ext, ['mp4', 'avi', 'mov']))
                                    <a href="{{ $url }}" target="_blank" class="text-blue-500 block mb-1">Xem video</a>
                                @endif
                                @endforeach
                    
                        @else
                            <span class="text-gray-500">Không có tệp</span>
                        @endif
                    </td>
                    <td class="border border-gray-300 dark:border-gray-700 px-2 py-5 items-center text-center">
                        @if ($refund->status === 'pending')
                            <form action="{{ route('admin.refunds.approve', $refund->refund_id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded-md mr-2">Duyệt</button>
                            </form>
                            <form action="{{ route('admin.refunds.reject', $refund->refund_id) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded-md">Từ chối</button>
                            </form>
                        @else
                            <span class="text-gray-500">Đã xử lý</span>
                        @endif
                    </td>
    
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="">
    {{ $refundRequests->links('pagination::tailwind') }}
</div>

@endsection
