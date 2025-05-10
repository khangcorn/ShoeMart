@extends('admin.layout')

@section('content')
<style>
    
</style>
<h1 class="text-2xl font-semibold mb-4">Danh sách yêu cầu hoàn tiền</h1>

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
{{-- Bảng yêu cầu hoàn tiền --}}
<table class="table-auto w-full border border-gray-300 mb-4">
    <thead>
        <tr>
            <th class="border px-4 py-2">Mã đơn hàng</th>
            <th class="border px-4 py-2">Người yêu cầu</th>
            <th class="border px-4 py-2">Lý do</th>
            <th class="border px-4 py-2">Tổng tiền</th>
            <th class="border px-4 py-2">Trạng thái</th>
            <th class="border px-4 py-2">Người duyệt</th>
            <th class="border px-4 py-2">Thời gian duyệt</th>
            <th class="border px-4 py-2">Tệp đính kèm</th>
            <th class="border px-4 py-2">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @foreach($refundRequests as $refund)
            <tr>
                <td class="border px-4 py-2">{{ $refund->order->order_code ?? 'N/A' }}</td>
                <td class="border px-4 py-2">{{ $refund->user->username ?? 'N/A' }}</td>
                <td class="border px-4 py-2">{{ $refund->reason }}</td>
                <td class="border px-4 py-2">{{ number_format($refund->amount, 0, ',', '.') }} đ</td>
                <td class="border px-4 py-2">
                    @if ($refund->status === 'approved')
                        Đã duyệt
                    @elseif ($refund->status === 'rejected')
                        Đã từ chối
                    @else
                        Chưa duyệt
                    @endif
                </td>
                
                
                <td class="border px-4 py-2">
                    @if ($refund->approved_by && $refund->approvedBy)
                        {{ $refund->approvedBy->username }}
                    @else
                        <span class="text-gray-500">Admin</span>
                    @endif
                </td>
                <td class="border px-4 py-2">
                    @if ($refund->approved_at)
                        {{ \Carbon\Carbon::parse($refund->approved_at)->format('d/m/Y H:i') }}
                    @else
                        <span class="text-gray-500">Chưa duyệt</span>
                    @endif
                </td>
                <td class="border px-4 py-2">
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
                <td class="border px-4 py-2 flex items-center justify-start">
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
@endsection
