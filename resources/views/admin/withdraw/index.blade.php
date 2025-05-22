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

    <style>
    /* Tạo kiểu cho phân trang */
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.pagination li {
    list-style: none;
    margin: 0 5px;
}

.pagination a, .pagination span {
    padding: 10px 15px;
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
}

.pagination .active a {
    background-color: #007bff;
    color: #fff;
    border-color: #007bff;
}


</style>
<div class="py-4 px-4">
 

    <table class="w-full ">
        <thead >
            <tr>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">#</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Người dùng</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Số tiền</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Trạng thái</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Ngày yêu cầu</th>
                <th class="px-2 py-5 border border-gray-300 text-center font-semibold">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($withdrawRequests as $withdraw)
                <tr class="text-center">
                    <td class="px-2 py-5 border border-gray-300 text-center font-semibold">{{ $withdraw->id }}</td>
                    <td class="px-2 py-5 border border-gray-300 text-center font-semibold">
                        {{ optional($withdraw->user)->username }}<br>
                        {{ optional($withdraw->user)->email }}
                    </td>
                    
                    <td class="px-2 py-5 border border-gray-300 text-center font-semibold">{{ number_format($withdraw->amount, 0, ',', '.') }}đ</td>
                    <td class="px-2 py-5 border border-gray-300 text-center font-semibold">
                        @if ($withdraw->status === 'pending')
                            <span class="text-yellow-500 font-semibold">Chờ duyệt</span>
                        @elseif ($withdraw->status === 'approved')
                            <span class="text-green-600 font-semibold">Đã duyệt</span>
                        @elseif ($withdraw->status === 'rejected')
                            <span class="text-red-500 font-semibold">Đã từ chối</span>
                        @endif
                    </td>
                    <td class="px-2 py-5 border border-gray-300 text-center font-semibold">{{ $withdraw->created_at->format('d/m/Y H:i') }}</td>

                            <td class="px-2 py-5 border border-gray-300 text-center font-semibold">
                                @if ($withdraw->status === 'pending')
                                    <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Duyệt</button>
                                    </form>

                                    <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">Từ chối</button>
                                    </form>
                                @else
                                    <em>Đã xử lý</em>
                                @endif
                            </td>
                </tr>
            @endforeach
        </tbody>
    </table>

     <div class="mt-6">
        {{ $withdrawRequests->links() }}
    </div>
</div>

@endsection
