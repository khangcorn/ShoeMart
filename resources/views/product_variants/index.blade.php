@extends('layout')

@section('content')
<div class="container">
    <h2>Danh sách biến thể sản phẩm</h2>
    <a href="{{ route('product_variants.create') }}" class="btn btn-primary">Thêm biến thể</a>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Giá</th>
                <th>Giá khuyến mãi</th>
                {{-- <th>% khuyến mãi</th> --}}
                <th>Tổng</th>
                <th>Tên biến thể</th>
                <th>Giá trị biến thể</th>
               
                <th>Hình ảnh</th> <!-- Thêm cột hiển thị ảnh -->
                <th>Hành động</th> <!-- Cột Hành động cho sửa và xóa -->
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->variant_id }}</td>
               <td>
                    {{ $variant->products ? $variant->products->name : 'Sản phẩm không tồn tại' }}
                </td>
                <td>{{ $variant->price }}</td>
                <td>{{ $variant->price_sale }}</td>
                {{-- <td>
                    {{-- <div>
                       
                        <td class="border border-gray-300 dark:border-gray-700  px-2 py-4 items-center text-center">
                            @if($variant->price_sale && $variant->price > 0)
                                {{ round((($variant->price - $variant->price_sale) / $variant->price) * 100, 2) }} %
                            @else
                                N/A
                            @endif
                        </td>
                    </div> --}}
                
                <td>{{ $variant->stock }}</td>

                <td>
                    @foreach($variant->attributes as $attribute)
                        <div>{{ $attribute->attribute_name }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach($variant->attributes as $attribute)
                        <div>{{ $attribute->attribute_value }}</div>
                    @endforeach
                </td>

                <!-- Hiển thị hình ảnh -->
                <td>
                    @foreach($variant->images as $image)
                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="Hình ảnh" width="100">
                    @endforeach
                </td>

                <!-- Cột Hành động Sửa và Xóa -->
                <td>
                    <a href="{{ route('product_variants.edit', $variant->variant_id) }}" class="btn btn-warning btn-sm">Sửa</a>
                    
                    <form action="{{ route('product_variants.destroy', $variant->variant_id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa biến thể này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
