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
            </tr>
        </thead>
        <tbody>
            @foreach($variants as $variant)
            <tr>
                <td>{{ $variant->id }}</td>
                <!-- Correcting this line to access the product's name -->
               
                <td>
                    <!-- Access the product directly, as it's a 'belongsTo' relationship -->
                    {{ $variant->products ? $variant->products->name : 'Sản phẩm không tồn tại' }}
                </td>
                
                <td>{{ $variant->price }}</td>
                <td>{{ $variant->price_sale }}</td>
                <td>{{ $variant->stock }}</td>

                <!-- Displaying variant attributes -->
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
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
