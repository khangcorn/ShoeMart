@extends('admin.layout')

@section('content')
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Edit size</h3>

        <form action="{{ route('sizes.update', $size->attribute_id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-gray-700 font-medium mb-2">Size Value:</label>
                <input type="text" name="attribute_value" value="{{ old('attribute_value', $size->attribute_value) }}" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            
                @if (session('error'))
                <div class="bg-red-500 text-white p-2 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            </div>
            

            <div class="flex justify-between">
                <a href="{{ route('sizes.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                    Back to List
                </a>

                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection
