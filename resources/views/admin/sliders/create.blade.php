@extends('admin.layout')

@section('title', 'Add Slider')

@section('content')
<div class="bg-white shadow-md rounded-lg max-w-lg mx-auto">
    <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
        <h4 class="text-lg font-semibold">Add New Slider</h4>
    </div>
    <div class="p-6">
        <form action="{{ route('sliders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Image</label>
                <input type="file" name="image_url" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer focus:ring-blue-500 focus:border-blue-500">
                @error('image_url') 
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div> 
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Caption</label>
                <textarea name="caption" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200"></textarea>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Link</label>
                <input type="text" name="link" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Position</label>
                <input type="number" name="position" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
            </div>
            <div class="flex justify-end space-x-2">
                <a href="{{ route('sliders.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">Back</a>
                <button type="submit" class="px-4 py-2 bg-red-600 text-black rounded-lg hover:bg-green-700 transition">Save</button>
            </div>
            
            
        </form>
    </div>
</div>
@endsection
