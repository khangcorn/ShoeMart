@extends('admin.layout')

@section('content')

@if(session('success'))
    <div id="flash-message" class="fixed top-5 right-5 bg-green-100 text-green-800 px-4 py-2 rounded shadow z-50">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const flash = document.getElementById('flash-message');
            if (flash) flash.remove();
        }, 5000);
    </script>
@endif

@if(session('error'))
    <div id="flash-error" class="fixed top-5 right-5 bg-red-500 text-white px-4 py-2 rounded shadow z-50">
        {{ session('error') }}
    </div>
    <script>
        setTimeout(() => {
            const flashError = document.getElementById('flash-error');
            if (flashError) flashError.remove();
        }, 5000);
    </script>
@endif

<style>
    /* Pagination styles */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        padding-left: 0;
    }
    .pagination li {
        list-style: none;
        margin: 0 5px;
    }
    .pagination a, .pagination span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 5px;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }
    .pagination a:hover, .pagination .active span {
        background-color: #007bff;
        color: #fff;
        border-color: #007bff;
    }
    .pagination .disabled span {
        color: #ccc;
        border-color: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="p-6">
    <h1 class="text-2xl font-semibold mb-6">Danh sách yêu cầu rút tiền</h1>

   <table class="w-full ">
                <thead class=" text-gray-700">
                    <tr class="border-b border-gray-300">
                <th class="border px-4 py-3">#</th>
                <th class="border px-4 py-3">Người dùng</th>
                <th class="border px-4 py-3">Số tiền</th>
                <th class="border px-4 py-3">Trạng thái</th>
                <th class="border px-4 py-3">Ngày yêu cầu</th>
                <th class="border px-4 py-3">Thao tác</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @forelse ($withdrawRequests as $withdraw)
                <tr class="text-center hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600">{{ $withdraw->id }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ optional($withdraw->user)->username ?? 'N/A' }}<br>
                        <small class="text-gray-500">{{ optional($withdraw->user)->email ?? 'N/A' }}</small>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ number_format($withdraw->amount, 0, ',', '.') }}đ</td>
                    <td class="py-3 px-4 border-b border-gray-300">
                        @switch($withdraw->status)
                            @case('pending')
                                <span class="text-yellow-500 font-semibold">Chờ duyệt</span>
                                @break
                            @case('approved')
                                <span class="text-green-600 font-semibold">Đã duyệt</span>
                                @break
                            @case('rejected')
                                <span class="text-red-500 font-semibold">Đã từ chối</span>
                                @break
                            @default
                                <span>Không rõ</span>
                        @endswitch
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $withdraw->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-3 px-4 border-b border-gray-300">
                        @if ($withdraw->status === 'pending')
                         @if(auth()->user()->hasPermission('approve_withdraw'))
                            <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Duyệt</button>
                            </form>
                            @endif
                             @if(auth()->user()->hasPermission('reject_withdraw'))
                            <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Từ chối</button>
                            </form>
                            @endif
                        @else
                            <em class="text-gray-600">Đã xử lý</em>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">Không có yêu cầu rút tiền nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-6">
        {{ $withdrawRequests->links() }}
    </div>
</div>

@endsection
