@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <h2 class="text-2xl font-bold mb-4">Chi tiết sản phẩm</h2>
    
    <div class="bg-white p-6 rounded-lg shadow-sm">
        <p class="mb-2"><span class="font-semibold">Tên sản phẩm:</span> {{ $product->name }}</p>
        <p class="mb-2"><span class="font-semibold">Giá:</span> {{ number_format($product->price, 0, ',', '.') }} vnđ</p>
        <p class="mb-2"><span class="font-semibold">Giá khuyến mãi:</span> {{ $product->price_sale ? number_format($product->price_sale, 0, ',', '.') : 'N/A' }} vnđ</p>
        <p class="mb-2"><span class="font-semibold">Số lượng tồn kho:</span> {{ $product->stock }}</p>
        <p class="mb-2"><span class="font-semibold">Danh mục:</span> {{ $product->category->name ?? 'Chưa có danh mục' }}</p>
        <p class="mb-4"><span class="font-semibold">Mô tả:</span> {{ $product->description }}</p>
        
        <h3 class="text-lg font-semibold mt-6 mb-2">Hình ảnh sản phẩm</h3>
        <div class="flex flex-wrap gap-3">
            @foreach($product->images as $image)
            <img src="{{ asset('storage/' . ltrim($image->image_url, '/storage/')) }}" alt="Hình ảnh sản phẩm" class="w-24 h-24 object-cover rounded border">
            @endforeach
        </div>

        <h3 class="text-lg font-semibold mt-6 mb-2">Biến thể</h3>
        <ul class="space-y-4">
            @foreach($product->variants as $variant)
            <li class="p-4 border rounded-lg">
                <p class="mb-1"><span class="font-semibold">ID biến thể:</span> {{ $variant->id }}</p>
                <p class="mb-1"><span class="font-semibold">Giá:</span> {{ number_format($variant->price, 0, ',', '.') }} vnđ</p>
                <p class="mb-1"><span class="font-semibold">Giá khuyến mãi:</span> {{ $variant->price_sale ? number_format($variant->price_sale, 0, ',', '.') : 'N/A' }} vnđ</p>
                <p class="mb-2"><span class="font-semibold">Tồn kho:</span> {{ $variant->stock }}</p>

                <h4 class="font-semibold mt-2">Thuộc tính:</h4>
                <div class="ml-4 space-y-1">
                    @foreach($variant->variantAttributeValues as $attributeValue)
                    <p><span class="font-medium">{{ $attributeValue->variantAttribute->attribute_name }}:</span> {{ $attributeValue->variantAttribute->attribute_value }}</p>
                    @endforeach
                </div>

                <h4 class="font-semibold mt-3">Hình ảnh biến thể:</h4>
                <div class="flex flex-wrap gap-3 mt-2 ml-2">
                    @foreach($variant->images as $image)
                    <img src="{{ asset('storage/' . ltrim($image->image_url, '/storage/')) }}" alt="Ảnh biến thể" class="w-24 h-24 object-cover rounded border">
                    @endforeach
                </div>
            </li>
            @endforeach
        </ul>

        <a href="{{ route('products.index') }}" class="mt-6 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded transition">
            Quay lại 
        </a>
    </div>
</div>
@endsection
