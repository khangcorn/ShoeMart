@extends('client.layout')

@section('content')
<div class="container">
    <h2>Chi tiết sản phẩm</h2>

    <!-- Thông tin sản phẩm -->
    <div id="product-details">
        <!-- Ảnh sản phẩm chính -->
        <img id="main-product-image" src="{{ asset('storage/' . ($product->images->first()->image_url ?? 'default-image.jpg')) }}" alt="{{ $product->name }}">
        <h3>{{ $product->name }}</h3>
        <p>{{ $product->description }}</p>
        <p>Giá: <span id="product-price">{{ number_format($product->price, 0, ',', '.') }} đ</span></p>

        <!-- Chọn biến thể nếu có -->
        <select id="variant-id" class="mt-4 mb-4">
            @foreach($product->variants as $variant)
                <option value="{{ $variant->id }}" 
                        data-price="{{ $variant->price }}" 
                        data-stock="{{ $variant->stock }} "
                        data-color="{{ $variant->color }}" 
                        data-size="{{ $variant->size }}">
                    {{ $variant->color }} - {{ $variant->size }} - {{ number_format($variant->price, 0, ',', '.') }} đ
                </option>
            @endforeach
        </select>

        <!-- Hiển thị thông tin biến thể đã chọn -->
        <p id="variant-details">
            Màu sắc: <span id="selected-color">Chưa chọn</span><br>
            Kích thước: <span id="selected-size">Chưa chọn</span><br>
            Số lượng tồn kho: <span id="stock-quantity">Chưa chọn</span>
        </p>
        <div class="variant-images-container flex space-x-4 mt-4">
            @foreach($product->variants as $variant)
            dd($variant-> $variant->id )
                <div class="variant-image-item" 
                     data-variant-id="{{ $variant->id }}"
                     data-color="{{ $variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Color')->variantAttribute->attribute_value }}" 
                     data-size="{{ $variant->variantAttributeValues->firstWhere('variantAttribute.attribute_name', 'Size')->variantAttribute->attribute_value }}" 
                     data-price="{{ $variant->price }}" 
                     data-stock="{{ $variant->stock }}"
                     data-images="{{ json_encode($variant->images->pluck('image_url')) }}">
                    <img src="{{ asset('storage/' . $variant->images->first()->image_url) }}" 
                         class="object-cover w-24 h-24 rounded-md cursor-pointer" 
                         onclick="updateProductDetails(this)">
                </div>
            @endforeach
        </div>

        <!-- Chọn số lượng -->
        <input type="number" id="quantity" value="1" min="1" max="100">

        <!-- Nút thêm vào giỏ -->
        <button id="add-to-cart-button" class="mt-6 bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition">
            Thêm vào giỏ hàng
        </button>
    </div>
</div>

<script>
 // Đảm bảo hàm updateProductDetails được định nghĩa trước khi sử dụng
function updateProductDetails(element) {
    // Lấy thông tin từ data-attributes của ảnh biến thể
    const variantItem = element.closest('.variant-image-item');
    if (!variantItem) return;

    const variantId = variantItem.getAttribute('data-variant-id');
    const color = variantItem.getAttribute('data-color');
    const size = variantItem.getAttribute('data-size');
    const price = variantItem.getAttribute('data-price');
    const stock = variantItem.getAttribute('data-stock');
    const images = JSON.parse(variantItem.getAttribute('data-images'));

    // Cập nhật thông tin hiển thị
    const mainImageElement = document.getElementById('main-product-image');
    if (mainImageElement) {
        mainImageElement.src = `{{ asset('storage/') }}/${images.length ? images[0] : 'default-image.jpg'}`;
    }

    document.getElementById('product-price').innerText = `${price} đ`;
    document.getElementById('selected-color').innerText = color;
    document.getElementById('selected-size').innerText = size;
    document.getElementById('stock-quantity').innerText = stock;
    document.getElementById('quantity').max = stock;

    // Cập nhật variant_id trong form
    document.getElementById('variant-id').value = variantId;
}

// Gắn sự kiện click vào ảnh biến thể
document.querySelectorAll('.variant-image-item img').forEach(img => {
    img.addEventListener('click', function() {
        updateProductDetails(this);
    });
});

// Xử lý sự kiện "Thêm vào giỏ hàng"
document.querySelector('#add-to-cart-button').addEventListener('click', function(e) {
    e.preventDefault();

    const productId = {{ $product->product_id }};  // Lấy ID sản phẩm từ biến PHP
    const variantId = document.querySelector('#variant-id').value;  // Lấy variant_id từ dropdown
    const quantity = document.querySelector('#quantity').value;

    // Kiểm tra nếu variant chưa được chọn hoặc số lượng không hợp lệ
    if (!variantId || variantId === '' || quantity <= 0 || quantity === '') {
        alert("Vui lòng chọn biến thể và số lượng hợp lệ!");
        return;
    }

    // Gửi yêu cầu thêm sản phẩm vào giỏ
    fetch("{{ route('cart.add') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: JSON.stringify({
            product_id: productId,
            variant_id: variantId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        // Hiển thị thông báo thành công
        alert(data.message);

        // Cập nhật giỏ hàng ngay lập tức
        const cartTableBody = document.querySelector('#cart-table tbody');
        cartTableBody.innerHTML = '';  // Xóa dữ liệu cũ trong giỏ hàng

        data.cartItems.forEach(item => {
            const row = `
                <tr>
                    <td class="px-4 py-2 border border-gray-300">${item.product.name}</td>
                    <td class="px-4 py-2 border border-gray-300">${item.variant.color} - ${item.variant.size}</td> 
                    <td class="px-4 py-2 border border-gray-300">${item.price} đ</td>
                    <td class="px-4 py-2 border border-gray-300">
                        <input type="number" name="quantity" value="${item.quantity}" min="1" class="quantity-input px-2 py-1 border border-gray-300 rounded-md">
                    </td>
                    <td class="px-4 py-2 border border-gray-300">${item.price * item.quantity} đ</td>
                    <td class="px-4 py-2 border border-gray-300">
                        <button class="delete-item-btn bg-red-500 text-white py-1 px-4 rounded-md hover:bg-red-600" data-item-id="${item.id}">Xóa</button>
                    </td>
                </tr>
            `;
            cartTableBody.insertAdjacentHTML('beforeend', row);
        });

        // Cập nhật tổng tiền
        document.getElementById('total-price').innerText = `Tổng tiền: ${data.total} đ`;
    })
    .catch(error => {
        console.error('Có lỗi xảy ra:', error);
    });
});

</script>


@endsection
