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

<div class="p-6">
    <h1 class="text-2xl font-semibold mb-4">Danh sách yêu cầu rút tiền</h1>

    <table class="w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="py-2 px-4 border">#</th>
                <th class="py-2 px-4 border">Người dùng</th>
                <th class="py-2 px-4 border">Số tiền</th>
                <th class="py-2 px-4 border">Trạng thái</th>
                <th class="py-2 px-4 border">Ngày yêu cầu</th>
                <th class="py-2 px-4 border">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($withdrawRequests as $withdraw)
                <tr class="text-center">
                    <td class="py-2 px-4 border">{{ $withdraw->id }}</td>
                    <td class="py-2 px-4 border">
                        {{ optional($withdraw->user)->username }}<br>
                        {{ optional($withdraw->user)->email }}
                    </td>
                    
                    <td class="py-2 px-4 border">{{ number_format($withdraw->amount, 0, ',', '.') }}đ</td>
                    <td class="py-2 px-4 border">
                        @if ($withdraw->status === 'pending')
                            <span class="text-yellow-500 font-semibold">Chờ duyệt</span>
                        @elseif ($withdraw->status === 'approved')
                            <span class="text-green-600 font-semibold">Đã duyệt</span>
                        @elseif ($withdraw->status === 'rejected')
                            <span class="text-red-500 font-semibold">Đã từ chối</span>
                        @endif
                    </td>
                    <td class="py-2 px-4 border">{{ $withdraw->created_at->format('d/m/Y H:i') }}</td>
                    <td class="py-2 px-4 border">
                        @if ($withdraw->status === 'pending')
                            <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="approve"> <!-- Sử dụng action thay vì status -->
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">Duyệt</button>
                            </form>
                    
                            <form action="{{ route('admin.withdraw.update', $withdraw->id) }}" method="POST" class="inline-block ml-2">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="action" value="reject"> <!-- Sử dụng action thay vì status -->
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

    <div class="mt-4">
        {{ $withdrawRequests->links() }}
    </div>
    
</div>

@endsection
