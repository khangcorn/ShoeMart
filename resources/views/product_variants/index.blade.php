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
                <th>Tổng</th>
                <th>Tên biến thể</th>
                <th>Giá trị biến thể</th>
                <th>Hình ảnh</th> <!-- Thêm cột hiển thị ảnh -->
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->variant_id }}</td>
                <td>{{ $variant->product ? $variant->product->name : 'Sản phẩm không tồn tại' }}</td>
                <td>{{ $variant->price }}</td>
                <td>{{ $variant->price_sale }}</td>
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
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
