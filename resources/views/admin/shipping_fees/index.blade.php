@extends('admin.layout')

@section('title', 'Shipping Fees')

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="flex justify-between items-center px-6 py-4 bg-blue-600 text-white rounded-t-lg">
        <h4 class="text-lg font-semibold">Sliders</h4>
        <a href="{{ route('shipping-fees.create') }}" class="bg-white text-blue-600 px-3 py-1 rounded-md text-sm font-medium hover:bg-gray-100 shadow">
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
                        <th class="border px-4 py-3">Province</th>
                        <th class="border px-4 py-3">District</th>
                        <th class="border px-4 py-3">Ward</th>
                        <th class="border px-4 py-3">Fee</th>
                        <th class="border px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($shippingFees as $fee)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-600">{{  $fee->shipping_id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-600">{{   $fee->province }}</td>
                       
                        <td class="px-4 py-3 text-gray-700">{{ $fee->district ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $fee->ward ?? '-' }}</td>
                   
                        <td class="px-4 py-3 text-gray-700">{{ number_format($fee->fee, 0, ',', '.') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('shipping-fees.edit', $fee->shipping_id) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-500 text-black text-xs font-medium rounded-md hover:bg-yellow-600 shadow">
                                    ✏️ Edit
                                </a>
                                <form action="{{ route('shipping-fees.destroy', $fee->shipping_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-500 text-white text-xs font-medium rounded-md hover:bg-red-600 shadow">
                                        🗑️ Delete
                                    </button>                                </form>
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
