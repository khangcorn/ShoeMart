@extends('admin.layout')

@section('content')
<div class="py-4 px-4">
    <h2 class="text-lg font-semibold">Product Details</h2>
    <div class="mt-4 p-4 border rounded-lg">
        <p><strong>Name:</strong> {{ $product->name }}</p>
        <p><strong>Price:</strong> {{ number_format($product->price, 0, ',', '.') }} vnđ</p>
        <p><strong>Sale Price:</strong> {{ $product->price_sale ? number_format($product->price_sale, 0, ',', '.') : 'N/A' }} vnđ</p>
        <p><strong>Stock:</strong> {{ $product->stock }}</p>
        <p><strong>Category:</strong> {{ $product->category->name ?? 'No category' }}</p>
        <p><strong>Description:</strong> {{ $product->description }}</p>
        
        <h3 class="mt-4 font-semibold">Images</h3>
        <div class="flex gap-2 mt-2">
            @foreach($product->images as $image)
            <img src="{{ asset('storage/' . ltrim($image->image_url, '/storage/')) }}" alt="Hình ảnh biến thể" width="100">
            @endforeach
        </div>
        
        <h3 class="mt-4 font-semibold">Variants</h3>
        <ul>
            @foreach($product->variants as $variant)
                <li class="border p-2 mt-2">
                    <p><strong>Variant ID:</strong> {{ $variant->id }}</p>
                    <p><strong>Price:</strong> {{ number_format($variant->price, 0, ',', '.') }} vnđ</p>
                    <p><strong>Sale Price:</strong> {{ $variant->price_sale ? number_format($variant->price_sale, 0, ',', '.') : 'N/A' }} vnđ</p>
                    <p><strong>Stock:</strong> {{ $variant->stock }}</p>
                    
                    <h4 class="mt-2 font-semibold">Attributes:</h4>
                    @foreach($variant->variantAttributeValues as $attribute)
                        <p><strong>{{ $attribute->attribute_name }}:</strong> {{ $attribute->attribute_value }}</p>
                    @endforeach
                    
                    <h4 class="mt-2 font-semibold">Variant Images:</h4>
                    <div class="flex gap-2 mt-2">
                        @foreach($variant->images as $image)
                        <img src="{{ asset('storage/' . ltrim($image->image_url, '/storage/')) }}" alt="Hình ảnh biến thể" width="100">
                        @endforeach
                    </div>
                </li>
            @endforeach
        </ul>
        
        <a href="{{ route('products.index') }}" class="mt-4 inline-block px-4 py-2 bg-blue-500 text-white rounded-lg">Back to List</a>
    </div>
</div>
@endsection
