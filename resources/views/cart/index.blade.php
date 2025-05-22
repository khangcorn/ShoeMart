@extends('client.layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-semibold mb-4">Giỏ Hàng</h2>

    @if($cartItems->isEmpty())
        <p class="text-lg">Giỏ hàng của bạn đang trống.</p>
    @else
        <table class="table-auto w-full border-collapse border border-gray-300" id="cart-table">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 border border-gray-300 text-center">
                        <input type="checkbox" id="select-all" onclick="toggleSelectAll()"> <!-- Chọn tất cả -->
                    </th>
                    <th class="px-4 py-2 border border-gray-300">Sản phẩm</th>
                    <th class="px-4 py-2 border border-gray-300">Giá</th>
                    <th class="px-4 py-2 border border-gray-300">Số lượng</th>
                    <th class="px-4 py-2 border border-gray-300">Tổng</th>
                    <th class="px-4 py-2 border border-gray-300">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cartItems as $item)
                @php
                    // Lấy giá của biến thể nếu có, nếu không lấy giá sản phẩm chính
                    $price = $item->variant 
                        ? ($item->variant->price_sale ?? $item->variant->price ?? $item->product->price) 
                        : ($item->product->price_sale ?? $item->product->price);
                @endphp
                <tr data-cart-detail-id="{{ $item->cart_detail_id }}">
                    <td class="px-4 py-2 border border-gray-300 text-center items-center">
                        <input type="checkbox" class="select-product" 
                               data-cart-detail-id="{{ $item->cart_detail_id }}" 
                               data-price="{{ $price * $item->quantity }}"
                               data-variant-id="{{ $item->variant ? $item->variant->id : '' }}"
                               onclick="toggleCartDetailId({{ $item->cart_detail_id }})">
                    </td>
                    
                    <td class="px-4 py-2 border border-gray-300 text-center items-center">
                        <a href="{{ route('products.detail', $item->product->product_id) }}" class="text-blue-500 hover:underline">
                            {{ $item->product->name }}
                        </a>
                    </td>
                    
                    <td class="px-4 py-2 border border-gray-300 text-center items-center">{{ number_format($price, 0, ',', '.') }} đ</td>
                    <td class="px-4 py-2 border border-gray-300 text-center items-center">
                        <input type="number" value="{{ $item->quantity }}" min="1"
                               class="quantity-input px-2 py-1 border border-gray-300 rounded-md w-16"
                               data-cart-detail-id="{{ $item->cart_detail_id }}"
                               data-stock="{{ $item->variant ? $item->variant->stock : $item->product->stock }}">
                    </td>
                    <td class="px-4 py-2 border border-gray-300 total-price text-center items-center">
                        {{ number_format($price * $item->quantity, 0, ',', '.') }} đ
                    </td>
                    <td class="px-4 py-2 border border-gray-300 text-center items-center">
                        <button class="delete-item bg-red-500 text-white py-1 px-4 rounded-md hover:bg-red-600"
                                data-cart-detail-id="{{ $item->cart_detail_id }}">
                            Xóa
                        </button>
                    </td>
                    
                    
                </tr>
                <input type="hidden" class="cart-detail-id" name="cart_detail_ids[]" value="{{ $item->cart_detail_id }}">
                @endforeach
            </tbody>
        </table>

        <div class="mt-6">
            <p class="font-semibold text-lg">
                <strong>Tổng tiền: </strong>
                <span id="selected-total-price">0</span> 
            </p>

            <button id="checkout-selected" class="mt-4 bg-blue-500 text-white py-2 px-6 rounded-md hover:bg-blue-600 transition">
                Thanh toán sản phẩm đã chọn
            </button>
            <button id="clear-cart" class="mt-4 bg-red-500 text-white py-2 px-6 rounded-md hover:bg-red-600">
                Xóa toàn bộ giỏ hàng
            </button>
            
        </div>        
    @endif
</div>
@endsection


<script>
   document.addEventListener("DOMContentLoaded", function () {
    function updateTotalPrice() {
        let total = 0;
        document.querySelectorAll(".select-product:checked").forEach(checkbox => {
            let row = checkbox.closest("tr");
            let totalCell = row.querySelector(".total-price");
            let totalPrice = parseFloat(totalCell.innerText.replace(/[^0-9]/g, "")) || 0;
            total += totalPrice;
        });
        document.getElementById("selected-total-price").innerText = `${total.toLocaleString('vi-VN')} đ`;
    }

    document.querySelectorAll(".quantity-input").forEach(input => {
        input.addEventListener("change", function () {
            let row = this.closest("tr");
            let cartDetailId = this.dataset.cartDetailId;
            let newQuantity = parseInt(this.value);
            let stock = parseInt(this.dataset.stock);
            let price = parseFloat(row.querySelector("td:nth-child(3)").innerText.replace(/[^0-9]/g, "")) || 0;

            if (newQuantity < 1) {
                alert("Số lượng phải lớn hơn 0");
                this.value = 1;
                return;
            }

            if (newQuantity > stock) {
                alert(`Chỉ còn ${stock} sản phẩm trong kho!`);
                this.value = stock;
                return;
            }

            fetch(`/cart/${cartDetailId}`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ quantity: newQuantity })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    let totalCell = row.querySelector(".total-price");
                    let total = price * newQuantity;
                    totalCell.innerText = `${total.toLocaleString('vi-VN')} đ`;
                    updateTotalPrice(); // 🔥 Cập nhật ngay sau khi thay đổi số lượng
                }
            })
            .catch(error => console.error("Có lỗi xảy ra khi cập nhật số lượng", error));
        });
    });

    // Lắng nghe sự kiện cho checkbox chọn sản phẩm
    document.querySelectorAll(".select-product").forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            let cartDetailId = this.getAttribute("data-cart-detail-id");
            let variantId = this.getAttribute("data-variant-id"); // Lấy id của biến thể (nếu có)
            console.log("Cart Detail ID:", cartDetailId);
            console.log("Variant ID:", variantId); // In ra variantId nếu có

            updateTotalPrice(); // Cập nhật tổng tiền khi thay đổi trạng thái
        });
    });

    // Lắng nghe sự kiện "Chọn tất cả"
    document.getElementById("select-all").addEventListener("change", function () {
        document.querySelectorAll(".select-product").forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotalPrice();
    });

    // Cập nhật số lượng giỏ hàng
    function updateCartCount() {
        fetch("/cart/count")
            .then(response => response.json())
            .then(data => {
                let cartCountElement = document.getElementById("cart-count");
                if (cartCountElement) {
                    if (data.count > 0) {
                        cartCountElement.innerText = data.count;
                        cartCountElement.style.display = "flex"; // Hiện số lượng nếu > 0
                    } else {
                        cartCountElement.style.display = "none"; // Ẩn nếu giỏ hàng trống
                    }
                }
            })
            .catch(error => console.error("Lỗi khi cập nhật số lượng giỏ hàng:", error));
    }

    // Lắng nghe sự kiện xóa sản phẩm
    document.querySelectorAll(".delete-item").forEach(button => {
        button.addEventListener("click", function () {
            let cartDetailId = this.dataset.cartDetailId;

            if (!confirm("Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?")) return;

            fetch(`/cart/${cartDetailId}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    let row = button.closest("tr");
                    row.remove(); // Xóa hàng khỏi bảng

                    // Kiểm tra nếu giỏ hàng trống thì ẩn luôn bảng
                    if (document.querySelectorAll("#cart-table tbody tr").length === 0) {
                        document.getElementById("cart-table").style.display = "none";
                        document.getElementById("clear-cart").style.display = "none";
                        document.getElementById("checkout-selected").style.display = "none";
                        document.querySelector(".text-lg").innerText = "Giỏ hàng của bạn đang trống.";
                    }
                    updateCartCount(); // 🔥 Cập nhật số lượng trên icon giỏ hàng
                } else {
                    alert("⚠️ " + data.message);
                }
            })
            .catch(error => {
                alert("🚨 Lỗi hệ thống! Vui lòng thử lại sau.");
            });
        });
    });

    // Lắng nghe sự kiện xóa toàn bộ giỏ hàng
    document.getElementById("clear-cart").addEventListener("click", function () {
        if (!confirm("Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?")) return;

        fetch("{{ route('cart.clear') }}", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                // Ẩn toàn bộ giao diện giỏ hàng
                document.getElementById("cart-table").style.display = "none";
                document.getElementById("clear-cart").style.display = "none";
                document.getElementById("checkout-selected").style.display = "none";

                // Hiển thị thông báo giỏ hàng trống
                let container = document.querySelector(".container");
                container.innerHTML += "<p class='text-lg'>Giỏ hàng của bạn đang trống.</p>";

                updateCartCount(); // 🔥 Cập nhật lại số lượng giỏ hàng về 0
            } else {
                alert("⚠️ " + data.message);
            }
        })
        .catch(error => {
            alert("🚨 Lỗi hệ thống! Vui lòng thử lại sau.");
        });
    });

    document.addEventListener("DOMContentLoaded", updateCartCount);

    // Lắng nghe sự kiện thanh toán sản phẩm đã chọn
    document.getElementById("checkout-selected").addEventListener("click", function () {
        let selectedItems = [];
        document.querySelectorAll(".select-product:checked").forEach(checkbox => {
            selectedItems.push(checkbox.getAttribute("data-cart-detail-id"));
        });

        if (selectedItems.length === 0) {
            alert("Vui lòng chọn ít nhất một sản phẩm để thanh toán.");
            return;
        }

        let checkoutUrl = "{{ route('cart.checkout') }}" + "?cart_detail_ids=" + selectedItems.join(",");
        window.location.href = checkoutUrl;
    });
});

    </script>
    