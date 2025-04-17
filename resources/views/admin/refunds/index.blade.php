@extends('admin.layout')

@section('content')
<h1 class="text-2xl font-semibold mb-4">Danh sách yêu cầu hoàn tiền</h1>

{{-- Hiển thị thông báo nếu có --}}
@if(session('success'))
    <div class="bg-green-500 text-white p-4 mb-4 rounded-md">
        {{ session('success') }}
    </div>
@endif

{{-- Hiển thị thông báo lỗi nếu có --}}
@if(session('error'))
    <div class="bg-red-500 text-white p-4 mb-4 rounded-md">
        {{ session('error') }}
    </div>
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
        @foreach($refunds as $refund)
            <tr>
                <td class="border px-4 py-2">{{ $refund->order->order_code }}</td>
                <td class="border px-4 py-2">{{ $refund->user->name }}</td> <!-- Người yêu cầu -->
                <td class="border px-4 py-2">{{ $refund->reason }}</td>
                <td class="border px-4 py-2">{{ number_format($refund->amount, 0, ',', '.') }} đ</td>
                <td class="border px-4 py-2">{{ ucfirst($refund->status) }}</td>
                <td class="border px-4 py-2">
                    @if ($refund->approved_by)
                        {{ $refund->approvedBy->username }}
                    @else
                        <span class="text-gray-500">Chưa duyệt</span>
                    @endif
                </td>
                <td class="border px-4 py-2">
                    @if ($refund->approved_at)
                        {{ $refund->approved_at->format('d/m/Y H:i') }}
                    @else
                        <span class="text-gray-500">Chưa duyệt</span>
                    @endif
                </td>
                <td class="border px-4 py-2">
                    @if($refund->attachments)
                    @foreach($refund->attachments as $attachment)
                        @php
                            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
                            $url = asset('storage/' . $attachment);
                        @endphp
                
                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                            <a href="{{ $url }}" target="_blank">
                                <img src="{{ $url }}" alt="Attachment" width="50">
                            </a>
                        @elseif (in_array($ext, ['mp4', 'avi', 'mov']))
                            <a href="{{ $url }}" target="_blank" class="text-blue-500">Xem video</a>
                        @endif
                    @endforeach
                @else
                    <span class="text-gray-500">Không có tệp đính kèm</span>
                @endif
                
                </td>
                <td class="border px-4 py-2">
                    @if ($refund->status == 'pending')
                        <form action="{{ route('refunds.approve', $refund->refund_id) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded-md">Duyệt</button>
                        </form>
                        <form action="{{ route('refunds.reject', $refund->refund_id) }}" method="POST" class="inline ml-2">
                            @csrf
                            @method('PATCH')
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
