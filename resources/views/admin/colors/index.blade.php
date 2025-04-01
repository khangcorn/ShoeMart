@extends('admin.layout')

@section('content')
    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Colors</h3>

        <div class="flex justify-end mb-4">
            <a href="{{ route('colors.create') }}"
                class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                + Add New Color
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="border border-gray-300 px-4 py-2 text-left">Color</th>
                        <th class="border border-gray-300 px-4 py-2 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($colors as $color)
                        <tr class="border border-gray-300 hover:bg-gray-50 transition">
                            <td class="border border-gray-300 px-4 py-2">{{ $color->attribute_value }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center flex justify-center space-x-2">
                                <a href="{{ route('colors.edit', $color->attribute_id) }}"
                                    class="px-3 py-1 bg-green-500 text-white rounded hover:bg-yellow-600 transition">
                                    Edit
                                </a>
                                <form action="{{ route('colors.destroy', $color->attribute_id) }}" method="POST" 
                                    onsubmit="return confirm('Are you sure you want to delete this color?');">
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
