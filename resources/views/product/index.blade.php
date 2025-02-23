@extends('layout')

@section('content')
<div class="container">
    <h2>Danh sách sản phẩm</h2>
    <a href="{{ route('products.create') }}" class="btn btn-primary">Thêm sản phẩm</a>
    
    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Mô tả</th>
                <th>Price</th>
                <th>Price_sale</th>
                <th>Stock</th>
                <th>Image</th>
                <th>Color</th>
                <th>Category</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->description }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->price_sale ?? 'Không có' }}</td>
                <td>{{ $product->stock }}</td>
                <td>
                    <img src="{{ asset('./storage/images' . $product->image_url) }}"  width="100">
                </td>
                
                <td>
                    @if($product->variants->isNotEmpty())
                        @foreach($product->variants as $variant)
                            @foreach($variant->attributes as $attribute)
                                @if($attribute->attribute_name == 'Color')
                                    {{ $attribute->attribute_value }}<br>
                                @endif
                            @endforeach
                        @endforeach
                    @else
                        Chưa có màu
                    @endif
                </td>
                <td>{{ $product->category->name ?? 'Không có danh mục' }}</td>
                <td>
                    <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning">Sửa</a>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
