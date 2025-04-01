@extends('admin.layout')

@section('content')
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6 mt-10">
        <h3 class="text-2xl font-semibold text-gray-700 mb-4 text-center">Create Size</h3>

        <form action="{{ route('sizes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-600 font-medium">Size Value:</label>
                <input type="text" name="attribute_value" required
                    class="w-full mt-2 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            <div class="flex justify-between">
                <a href="{{ route('sizes.index') }}" class="text-blue-500 hover:underline">
                    ← Back to List
                </a>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition">
                    Create
                </button>
            </div>
        </form>
    </div>
@endsection
