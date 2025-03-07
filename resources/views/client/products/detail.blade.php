<h1>{{ $products->name }}</h1>
<p>{{ $products->description }}</p>

<h3>Category: {{ $products->category->name }}</h3>

<h4>Variants:</h4>
<ul>
    @foreach($products->variants as $variant)
        <li>
            <strong>{{ $variant->name }}</strong>
            <p>Price: {{ number_format($variant->price, 0, ',', '.') }} VND</p>
            
            @if($variant->price_sale)
                <p>Sale Price: {{ number_format($variant->price_sale, 0, ',', '.') }} VND</p>
            @else
                <p>No Sale Price</p>
            @endif
            
            <ul>
                @foreach($variant->attributes as $attribute)
                    <li>{{ $attribute->attribute_name }}: {{ $attribute->attribute_value }}</li>
                @endforeach
            </ul>
        </li>
    @endforeach
</ul>

<h4>Images:</h4>
<ul>
    @foreach($products->images as $image)
        <li><img src="{{ asset('storage/' . $image->image_url) }}" alt="Product Image"></li>
    @endforeach
</ul>
