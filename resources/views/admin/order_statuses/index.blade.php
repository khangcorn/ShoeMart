@extends('admin.layout')

@section('title', 'Order Statuses')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white rounded-t-lg">
        <h4 class="text-lg font-semibold">Trạng thái đơn hàng</h4>
        <a href="{{ route('order-statuses.create') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 shadow">
            + Thêm trạng thái
        </a>
    </div>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-300 text-green-700 px-4 py-3 rounded relative" role="alert">
                {{ session('success') }}
                <button type="button" class="absolute top-2 right-2 text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
                    &times;
                </button>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-center border-collapse border border-gray-200 rounded-lg shadow-sm">
                <thead class="bg-gray-100 text-gray-700">
                    <tr class="border-b border-gray-300">
                        <th class="border px-4 py-3">#</th>
                        <th class="border px-4 py-3">Tên trạng thái</th>
                        <th class="border px-4 py-3">Mô tả</th>
                        <th class="border px-4 py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($statuses as $status)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-600">{{ $status->status_id }}</td>
                            <td class="px-4 py-3 font-medium text-gray-700">{{ $status->name }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $status->description }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('order-statuses.edit', $status->status_id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-black text-xs font-medium rounded-md hover:bg-yellow-600 shadow">
                                        ✏️ Sửa
                                    </a>
                                    <form action="{{ route('order-statuses.destroy', $status->status_id) }}" method="POST" onsubmit="return confirm('Xóa trạng thái này?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 shadow">
                                            🗑️ Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-gray-500">Không có trạng thái nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
