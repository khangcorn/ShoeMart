@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Danh sách địa chỉ của tôi</h2>

    @if($addresses->isEmpty())
        <p>Hiện tại bạn chưa có địa chỉ nào. <a href="{{ route('address.create') }}" class="text-blue-500">Thêm địa chỉ mới</a>.</p>
    @else
        <ul class="space-y-4">
            @foreach($addresses as $address)
                <li class="border p-4 rounded-md">
                    <strong>{{ $address->address_name }}</strong><br>
                    {{ $address->recipient_name }}<br>
                    {{ $address->street_address }}, {{ $address->ward }}, {{ $address->district }}, {{ $address->city }}<br>
                    
                    <a href="{{ route('address.edit', $address->address_id) }}" class="text-blue-500 underline">Sửa địa chỉ</a> | 
                    
                    <!-- Form chọn làm mặc định -->
                    <form action="{{ route('address.setDefault', $address->address_id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="text-green-500 underline">Chọn làm mặc định</button>
                    </form>
                    |
                    
                    <!-- Form xóa địa chỉ -->
                    <form action="{{ route('address.delete', $address->address_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa địa chỉ này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 underline">Xóa địa chỉ</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection
