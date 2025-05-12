@extends('admin.layout')

@section('content')
<style>
    /* Đảm bảo rằng container chứa các nút có display flex và căn giữa */
.mt-6.flex {
    display: flex;
    justify-content: center; /* Căn giữa các nút */
    gap: 16px; /* Khoảng cách giữa các nút */
}
.alert-error-custom {
        color: #721c24;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        padding: 12px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        margin-top: 10px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-custom {
        display: inline-block;
        padding: 10px 18px;
        font-size: 15px;
        font-weight: 500;
        border-radius: 8px;
        text-decoration: none;
        text-align: center;
        transition: all 0.2s ease-in-out;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        border: none;
        margin-right: 8px;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .btn-success {
        background-color: #10b981;
        color: white;
    }

    .btn-success:hover {
        background-color: #059669;
    }

    .btn-secondary {
        background-color: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #4b5563;
    }

</style>
@if ($errors->has('image_format'))
    <div id="error-messages" class="alert-error-custom">
        {{ $errors->first('image_format') }}
    </div>
@endif
@if ($errors->has('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
        {{ $errors->first('error') }}
    </div>
@endif



<div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-6">Thêm Sản Phẩm Mới và Biến Thể</h1>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
    
        <!-- Main Product Fields -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Tên Sản Phẩm</label>
            <input type="text" id="name" name="name"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror"
                value="{{ old('name') }}">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700">Mô tả</label>
            <input type="text" id="description" name="description"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror"
                value="{{ old('description') }}">
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="price" class="block text-sm font-medium text-gray-700">Giá</label>
            <input type="number" id="price" name="price"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('price') border-red-500 @enderror"
                value="{{ old('price') }}">
            @error('price')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="price_sale" class="block text-sm font-medium text-gray-700">Giá Khuyến Mãi</label>
            <input type="number" id="price_sale" name="price_sale"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('price_sale') border-red-500 @enderror"
                value="{{ old('price_sale') }}">
            @error('price_sale')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div id="product_stock_input" class="hidden">
            <label for="total_stock_input" class="block text-sm font-medium text-gray-700">Số Lượng</label>
            <input type="number" name="stock" id="total_stock_input"
                class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('stock') border-red-500 @enderror"
                value="{{ old('stock') }}">
            @error('stock')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700">Danh Mục</label>
            <select class="text-black w-full mt-1 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 @error('category_id') border-red-500 @enderror"
                id="category_id" name="category_id">
                <option value="">Chọn Danh Mục</option>
                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
    
        <div>
            <label for="product_images" class="block text-sm font-medium text-gray-700 mb-2">Hình Ảnh Sản Phẩm Chính</label>
        
            <label for="product_images"
                class="w-24 h-28 border-2 border-dashed border-gray-300 rounded-lg flex flex-col items-center justify-center cursor-pointer hover:border-red-500
                       @error('product_images') border-red-500 @enderror">
                <span class="text-2xl font-bold text-gray-500">+</span>
                <span class="text-sm text-gray-600 mt-1">Tải tệp</span>
                <input type="file" id="product_images" name="product_images[]" multiple class="hidden" onchange="handleFiles(event)">
            </label>
        
            @error('product_images')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        
            <div id="preview" class="mt-4 flex gap-2 flex-wrap"></div>
        </div>
    
        <div id="variant_fields" class="space-y-4"></div>
    
       <!-- Nút thêm biến thể -->
        <div class="mt-6 flex justify-center gap-4">
            <button type="button" id="add_variant_btn"
                class="inline-flex items-center bg-blue-600 text-white font-semibold text-sm px-5 py-2 rounded-md hover:bg-blue-700 shadow-md transition">
                Thêm Biến Thể
            </button>

            <!-- Nút lưu -->
            <button type="submit"
                class="inline-flex items-center bg-green-500 text-white font-semibold text-sm px-6 py-2 rounded-md hover:bg-green-600 shadow-md transition">
                Lưu
            </button>

            <!-- Nút quay lại -->
            <a href="{{ route('products.index') }}"
                class="inline-flex items-center bg-gray-500 text-white font-semibold text-sm px-6 py-2 rounded-md hover:bg-gray-600 shadow-md transition">
                Quay lại    
            </a>
        </div>

    </form>
    
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addVariantBtn = document.getElementById('add_variant_btn');
    const variantFieldsContainer = document.getElementById('variant_fields');
    const productStockField = document.getElementById('product_stock_input'); // Lấy phần tử input số lượng sản phẩm
    let variantIndex = 0;
    let colorImages = {}; // Lưu ảnh theo màu

    // Đảm bảo rằng trường "Số Lượng" của sản phẩm chính luôn hiển thị khi chưa thêm biến thể
    productStockField.classList.remove('hidden');

    addVariantBtn.addEventListener('click', function () {
        const newVariant = document.createElement('div');
        newVariant.classList.add('variant', 'mt-3', 'border', 'p-3', 'rounded-lg', 'shadow-md');

        newVariant.innerHTML = `
            <div class="form-group mb-4">
    <label for="variant-price-${variantIndex}" class="text-gray-700 font-semibold">Giá</label>
    <input id="variant-price-${variantIndex}" type="number" class="form-control variant-price border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][price]" value="">
</div>

<div class="form-group mb-4">
    <label for="variant-sale-price-${variantIndex}" class="text-gray-700 font-semibold">Giá Khuyến Mãi</label>
    <input id="variant-sale-price-${variantIndex}" type="number" class="form-control variant-sale-price border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][price_sale]" value="">
</div>

<div class="form-group mb-4">
    <label for="variant-stock-${variantIndex}" class="text-gray-700 font-semibold">Số lượng</label>
    <input id="variant-stock-${variantIndex}" type="number" class="form-control variant-stock border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][stock]" value="0">
</div>

<div class="form-group mb-4">
    <label for="variant-color-${variantIndex}" class="text-gray-700 font-semibold">Màu sắc</label>
    <select id="variant-color-${variantIndex}" class="form-control variant-color border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][color]">
        <option value="">Chọn màu</option>
        @foreach($colors as $color)
            <option value="{{ $color->attribute_value }}">{{ $color->attribute_value }}</option>
        @endforeach
    </select>
</div>

<div class="form-group mb-4">
    <label for="variant-size-${variantIndex}" class="text-gray-700 font-semibold">Kích cỡ</label>
    <select id="variant-size-${variantIndex}" class="form-control variant-size border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500 text-black" name="variants[${variantIndex}][size]">
        <option value="">Chọn kích cỡ</option>
        @foreach($sizes as $size)
            <option value="{{ $size->attribute_value }}">{{ $size->attribute_value }}</option>
        @endforeach
    </select>
</div>

<div class="form-group mb-4">
    <label for="variant-images-${variantIndex}" class="text-gray-700 font-semibold">Hình Ảnh Biến Thể (Tối đa 5 ảnh)</label>
    <input type="file" class="form-control variant-image-input border border-gray-300 rounded-lg p-2 w-full focus:outline-none focus:ring-2 focus:ring-indigo-500" id="variant-images-${variantIndex}" name="variant_images_${variantIndex}[]" multiple accept="image/*">
    <div class="image-preview mt-2" id="image-preview-${variantIndex}"></div>
</div>

<p class="text-danger error-message text-red-500 d-none" style="display: none;">⚠️ Biến thể với Màu và Size này đã tồn tại!</p>

<button type="button" class="btn btn-danger mt-4 bg-red-500 text-white rounded-lg px-4 py-2 hover:bg-red-600 focus:outline-none remove-variant">Xóa Biến Thể</button>

        `;

        variantFieldsContainer.appendChild(newVariant);
        variantIndex++;

        // Ẩn trường số lượng của sản phẩm chính khi thêm biến thể
        productStockField.classList.add('hidden'); 

        const errorMsg = newVariant.querySelector('.error-message');
        errorMsg.classList.add('d-none');
        errorMsg.style.display = "none";

        const colorSelect = newVariant.querySelector('.variant-color');
        const sizeSelect = newVariant.querySelector('.variant-size');
        const imageInput = newVariant.querySelector('.variant-image-input');
        const imagePreview = newVariant.querySelector('.image-preview');

        colorSelect.addEventListener('change', function () {
            checkDuplicateVariant();
            autoFillImages(newVariant);
        });

        sizeSelect.addEventListener('change', function () {
            checkDuplicateVariant();
        });

        imageInput.addEventListener('change', function (event) {
            let color = colorSelect.value;
            if (color) {
                let files = event.target.files;
                if (files.length > 0) {
                    colorImages[color] = files;
                    displayImages(imagePreview, files);
                }
            }
        });

        newVariant.querySelector('.remove-variant').addEventListener('click', function () {
            newVariant.remove();
            checkDuplicateVariant();

            // Kiểm tra lại và hiển thị lại trường số lượng khi không còn biến thể nào
            productStockField.classList.remove('hidden'); // Hiển thị lại trường số lượng khi không còn biến thể
        });

        // Kiểm tra số lượng biến thể mỗi lần thêm biến thể
        checkDuplicateVariant();
    });

    function checkDuplicateVariant() {
        let existingVariants = new Set();

        document.querySelectorAll('.variant').forEach(variant => {
            const color = variant.querySelector('.variant-color').value;
            const size = variant.querySelector('.variant-size').value;
            const errorMsg = variant.querySelector('.error-message');

            errorMsg.classList.add('d-none');
            errorMsg.style.display = "none";

            if (color && size) {
                const key = `${color}-${size}`;
                if (existingVariants.has(key)) {
                    errorMsg.classList.remove('d-none');
                    errorMsg.style.display = "block";
                } else {
                    existingVariants.add(key);
                }
            }
        });
    }

    function autoFillImages(variantElement) {
        let color = variantElement.querySelector('.variant-color').value;
        let imagePreview = variantElement.querySelector('.image-preview');
        let imageInput = variantElement.querySelector('.variant-image-input');

        imagePreview.innerHTML = "";
        imageInput.value = "";

        if (colorImages[color]) {
            displayImages(imagePreview, colorImages[color]);
        }
    }

    function displayImages(previewContainer, files) {
        previewContainer.innerHTML = "";
        Array.from(files).forEach(file => {
            let img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.width = "80px";
            img.style.marginRight = "5px";
            img.style.borderRadius = "8px";
            previewContainer.appendChild(img);
        });
    }

    // ✅ VALIDATION FORM TRƯỚC KHI SUBMIT
    document.querySelector('form').addEventListener('submit', function (event) {
    let isValid = true;

    // Kiểm tra nếu có thông báo lỗi đang hiển thị
    if (document.querySelector('.error-message:not(.d-none)')) {
        alert('Có biến thể bị trùng Color & Size. Hãy kiểm tra lại!');
        event.preventDefault();
        return;
    }

    // Kiểm tra trùng lặp giữa các biến thể (Color và Size)
    let colorSizePairs = [];
    document.querySelectorAll('.variant').forEach(variant => {
        const color = variant.querySelector('.variant-color').value;
        const size = variant.querySelector('.variant-size').value;
        
        if (color && size) {
            let pair = `${color}-${size}`;
            if (colorSizePairs.includes(pair)) {
                showError(variant.querySelector('.variant-color'), 'Màu và Kích cỡ này đã tồn tại');
                showError(variant.querySelector('.variant-size'), 'Màu và Kích cỡ này đã tồn tại');
                isValid = false;
            } else {
                colorSizePairs.push(pair);
            }
        }

        // Kiểm tra xem có ảnh không (ít nhất 1 ảnh cho biến thể)
        const imagesInput = variant.querySelector('.variant-image-input');
        if (imagesInput && imagesInput.files.length === 0) {
            showError(imagesInput, 'Vui lòng tải lên ít nhất 1 ảnh cho biến thể');
            isValid = false;
        }
    });

    // Kiểm tra các trường dữ liệu khác (Giá, Số lượng, Màu, Kích cỡ)
    document.querySelectorAll('.variant').forEach(variant => {
        const price = variant.querySelector('.variant-price');
        const salePrice = variant.querySelector('.variant-sale-price');
        const stock = variant.querySelector('.variant-stock');
        const color = variant.querySelector('.variant-color');
        const size = variant.querySelector('.variant-size');

        clearError(price);
        clearError(salePrice);
        clearError(stock);
        clearError(color);
        clearError(size);

        if (price.value.trim() === '' || parseFloat(price.value) < 0) {
            showError(price, 'Giá không hợp lệ');
            isValid = false;
        }

        if (salePrice.value !== '' && parseFloat(salePrice.value) < 0) {
            showError(salePrice, 'Giá khuyến mãi không hợp lệ');
            isValid = false;
        }

        if (stock.value.trim() === '' || parseInt(stock.value) < 0) {
            showError(stock, 'Số lượng không hợp lệ');
            isValid = false;
        }

        if (!color.value) {
            showError(color, 'Vui lòng chọn màu');
            isValid = false;
        }

        if (!size.value) {
            showError(size, 'Vui lòng chọn kích cỡ');
            isValid = false;
        }
    });

    if (!isValid) {
        event.preventDefault();
        alert('Vui lòng điền đầy đủ và hợp lệ tất cả các trường của biến thể.');
        return;
    }

    const formData = new FormData();
    const form = document.querySelector('form');
    const formElements = form.querySelectorAll('input, select, textarea');

    formElements.forEach(element => {
        if (element.name && element.type !== 'file') {
            formData.append(element.name, element.value);
        }
    });

    const imageInput = document.querySelector('input[name="product_images[]"]');
    const files = imageInput.files;

    if (files.length === 0) {
        alert('Vui lòng chọn ảnh sản phẩm');
        return; // Dừng việc gửi dữ liệu nếu không có ảnh
    }

    for (let i = 0; i < files.length; i++) {
        formData.append('product_images[]', files[i]);
    }

    // Thêm file ảnh thủ công
    document.querySelectorAll('.variant').forEach((variant, index) => {
        const imageInput = variant.querySelector('.variant-image-input');
        const files = imageInput.files;

        for (let i = 0; i < files.length; i++) {
            formData.append(`variant_images_${index}[]`, files[i]);
        }
    });

    // Gửi dữ liệu qua Ajax (bạn có thể gửi formData qua một API hoặc đến server)
    fetch('/admin/products', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Không thể gửi yêu cầu');
        }

        // Kiểm tra kiểu dữ liệu trả về từ server
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Dữ liệu trả về không phải JSON');
        }

        return response.json();
    })
    .then(data => {
        console.log('Dữ liệu đã được gửi thành công:', data);
           // Hiển thị thông báo thành công
        const successMessage = data.success || 'Sản phẩm đã được tạo thành công!';

        // Lưu thông báo vào localStorage
        localStorage.setItem('success_message', successMessage);

        // Chuyển hướng về trang index
        window.location.href = data.redirect_url;

    })
    .catch(error => {
    console.error('Lỗi chi tiết:', error); // Debug lỗi chi tiết

    if (error.response && error.response.data) {
        // Lỗi từ backend
        const errors = error.response.data.errors;
        
        if (errors && errors.image_format) {
            alert(errors.image_format); // Hiển thị lỗi từ backend
        } else {
            alert('Lỗi xảy ra khi gửi yêu cầu!'); // Lỗi chung
        }
    } else {
        alert('Lỗi xảy ra khi gửi yêu cầu!');
    }
});

    // Ngừng gửi form mặc định (chỉ gửi qua Ajax)
    event.preventDefault();
});



    function showError(inputEl, message) {
        let errorEl = document.createElement('p');
        errorEl.className = 'text-red-500 text-sm mt-1 input-error';
        errorEl.textContent = message;
        inputEl.classList.add('border-red-500');
        inputEl.parentNode.appendChild(errorEl);
    }

    function clearError(inputEl) {
        inputEl.classList.remove('border-red-500');
        const errorEl = inputEl.parentNode.querySelector('.input-error');
        if (errorEl) errorEl.remove();
    }
});

if (document.getElementById('error-messages')) {
        setTimeout(function() {
            document.getElementById('error-messages').style.display = 'none';
        }, 5000); // 5000ms = 5 giây
    }

</script>

    
@endsection