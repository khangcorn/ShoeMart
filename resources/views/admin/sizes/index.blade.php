@extends('admin.layout')

@section('content')
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Sizes</h3>

        {{-- Hiển thị thông báo lỗi nếu có --}}
        @if (session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        {{-- Hiển thị thông báo thành công nếu có --}}
        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-end mb-4">
            <a href="{{ route('sizes.create') }}"
                class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                + Add New Size
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 px-4 py-2 text-left">Size</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sizes as $size)
                        <tr class="border border-gray-300 hover:bg-gray-50 transition">
                            <td class="border border-gray-300 px-4 py-2">{{ $size->attribute_value }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center flex justify-center space-x-2">
                                <a href="{{ route('sizes.edit', $size->attribute_id) }}"
                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-yellow-600 transition">
                                    Edit
                                </a>
                                <form action="{{ route('sizes.destroy', $size->attribute_id) }}" method="POST" 
                                    onsubmit="return confirm('Bạn có chắc muốn xóa Size này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
