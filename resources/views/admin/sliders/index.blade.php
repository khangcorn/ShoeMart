@extends('admin.layout')

@section('title', 'Sliders List')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white rounded-t-lg">
        <h4 class="text-lg font-semibold">Sliders</h4>
        <a href="{{ route('sliders.create') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 shadow">
            + Add Slider
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
                        <th class="border px-4 py-3">Image</th>
                        <th class="border px-4 py-3">Caption</th>
                        <th class="border px-4 py-3">Link</th>
                        <th class="border px-4 py-3">Position</th>
                        <th class="border px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($sliders as $slider)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-600">{{ $slider->slider_id }}</td>
                        <td class="px-4 py-3">
                            <img src="{{ asset($slider->image_url) }}" class="w-20 h-20 object-cover rounded-md shadow-md">
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $slider->caption }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ $slider->link }}" target="_blank" class="text-blue-600 hover:underline">
                                {{ Str::limit($slider->link, 30) }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $slider->position }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('sliders.edit', $slider->slider_id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-black text-xs font-medium rounded-md hover:bg-yellow-600 shadow">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('sliders.destroy', $slider->slider_id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 shadow">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-gray-500">No sliders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
