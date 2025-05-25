@extends('admin.layout')

@section('content')
<style>
  /* Nâng cấp style flash message */
  #flash-message, #flash-error {
    position: fixed;
    top: 1rem;
    right: 1rem;
    padding: 0.75rem 1.25rem;
    border-radius: 0.375rem;
    font-weight: 600;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 9999;
    cursor: pointer;
  }
  #flash-message {
    background-color: #d1fae5; /* xanh lá nhạt */
    color: #065f46; /* xanh đậm */
  }
  #flash-error {
    background-color: #fecaca; /* đỏ nhạt */
    color: #7f1d1d; /* đỏ đậm */
  }
  .badge {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 9999px;
  }
  .badge-pending {
    background-color: #fbbf24; /* vàng */
    color: #92400e;
  }
  .badge-approved {
    background-color: #22c55e; /* xanh */
    color: #166534;
  }
  .badge-rejected {
    background-color: #ef4444; /* đỏ */
    color: #991b1b;
  }
  .attachments img {
    border-radius: 0.25rem;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    max-width: 50px;
    max-height: 50px;
    object-fit: cover;
    box-shadow: 0 0 3px rgba(0,0,0,0.1);
  }
  .attachments a.video-link {
    display: block;
    color: #2563eb;
    margin-bottom: 0.25rem;
    font-weight: 600;
  }
  /* Nút hover */
  .btn-approve {
    @apply bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition;
  }
  .btn-reject {
    @apply bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition;
  }
</style>

<h1 class="text-2xl font-semibold mb-6">Danh sách yêu cầu hoàn tiền</h1>

@if(session('success'))
  <div id="flash-message" role="alert" tabindex="0">{{ session('success') }}</div>
@endif

@if(session('error'))
  <div id="flash-error" role="alert" tabindex="0">{{ session('error') }}</div>
@endif

<table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr class="border-b border-gray-300">
      <th class="border px-4 py-3">Mã đơn hàng</th>
      <th class="border px-4 py-3">Người yêu cầu</th>
      <th class="border px-4 py-3">Lý do</th>
      <th class="border px-4 py-3">Tổng tiền</th>
      <th class="border px-4 py-3">Trạng thái</th>
      <th class="border px-4 py-3">Người duyệt</th>
      <th class="border px-4 py-3">Thời gian duyệt</th>
      <th class="border px-4 py-3">Tệp đính kèm</th>
      <th class="border px-4 py-3">Thao tác</th>
    </tr>
  </thead>
  <tbody>
    @foreach($refundRequests as $refund)
    <tr >
       <td class="border px-4 py-2 text-center">
                            <a href="{{ route('order.show', $refund->order_id) }}" class="text-blue-600 hover:underline" target="_blank">
                                {{ $refund->order->order_code ?? 'N/A'}}
                            </a>
                        </td>
     
      <td class="border px-4 py-2 text-center">{{ $refund->user->username ?? 'N/A' }}</td>
      <td class="border px-4 py-2 max-w-xs break-words">{{ $refund->reason }}</td>
      <td class="border px-4 py-2 text-right">{{ number_format($refund->amount, 0, ',', '.') }} đ</td>
      <td class="border px-4 py-2 text-center">
        @if ($refund->status === 'approved')
          <span class="badge badge-approved">Đã duyệt</span>
        @elseif ($refund->status === 'rejected')
          <span class="badge badge-rejected">Đã từ chối</span>
        @else
          <span class="badge badge-pending">Chưa duyệt</span>
        @endif
      </td>
      <td class="border px-4 py-2 text-center">
        @isset($refund->approvedBy)
          {{ $refund->approvedBy->username }}
        @else
          <span class="text-gray-500 italic">Admin</span>
        @endisset
      </td>
      <td class="border px-4 py-2 text-center">
        @if ($refund->approved_at)
          {{ \Carbon\Carbon::parse($refund->approved_at)->format('d/m/Y H:i') }}
        @else
          <span class="text-gray-500 italic">Chưa duyệt</span>
        @endif
      </td>
      <td class="border px-4 py-2 attachments text-center">
        @if($refund->attachments)
          @foreach(json_decode($refund->attachments) as $attachment)
            @php
              $ext = strtolower(pathinfo($attachment, PATHINFO_EXTENSION));
              $url = asset('storage/' . $attachment);
            @endphp
            @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
              <a href="{{ $url }}" target="_blank" rel="noopener" title="Xem ảnh đính kèm">
                <img src="{{ $url }}" alt="Attachment">
              </a>
            @elseif (in_array($ext, ['mp4', 'avi', 'mov']))
              <a href="{{ $url }}" target="_blank" rel="noopener" class="video-link" title="Xem video đính kèm">Xem video</a>
            @endif
          @endforeach
        @else
          <span class="text-gray-500 italic">Không có tệp</span>
        @endif
      </td>
      <td class="border px-4 py-2 text-center flex justify-center gap-2">
        @if ($refund->status === 'pending')
         @if(auth()->user()->hasPermission('process_refund'))
          <form action="{{ route('admin.refunds.approve', $refund->refund_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn duyệt yêu cầu này?')">
            @csrf
            <button type="submit" class="btn-approve">Duyệt</button>
          </form>
          @endif
           @if(auth()->user()->hasPermission('reject_refund'))
          <form action="{{ route('admin.refunds.reject', $refund->refund_id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc muốn từ chối yêu cầu này?')">
            @csrf
            <button type="submit" class="btn-reject">Từ chối</button>
          </form>
          @endif
        @else
          <span class="text-gray-500 italic">Đã xử lý</span>
        @endif
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

 <div class="mt-6 flex justify-center">
  {{ $refundRequests->links('pagination::tailwind') }}
</div>

<script>
  // Ẩn flash messages sau 5s khi click cũng ẩn
  document.querySelectorAll('#flash-message, #flash-error').forEach(el => {
    setTimeout(() => {
      el.style.transition = 'opacity 0.5s ease';
      el.style.opacity = '0';
      setTimeout(() => el.remove(), 500);
    }, 5000);
    el.addEventListener('click', () => {
      el.remove();
    });
  });
</script>
@endsection
