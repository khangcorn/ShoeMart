@extends('layout')

@section('content')
    <div class="container">
        <form action="{{ route('product_variants.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="product_id">Sản phẩm</label>
                <select class="form-control" id="product_id" name="product_id" required>
                    <option value="">Chọn Sản Phẩm</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="variants">
                <div class="variant">
            
                  
                    
    

                    @error('variants.*.images')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror

                    <button type="button" id="add-variant" class="btn btn-primary mt-2">Thêm Biến Thể</button>
                    
                    <button type="submit" class="btn btn-success mt-2">Lưu</button>
                    <a href="{{ route('product_variants.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
                </div>
            </div>
        </form>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        let variantContainer = document.getElementById("variants");
        let variantIndex = 1;

        document.getElementById("add-variant").addEventListener("click", function() {
            let variantDiv = document.createElement("div");
            variantDiv.classList.add("variant");

            variantDiv.innerHTML = `
                <hr>
                <div class="form-group">
                    <label for="variant_price">Giá</label>
                    <input type="number" class="form-control" name="variants[${variantIndex}][price]" required>
                </div>
                <div class="form-group">
                    <label for="variant_price_sale">Giá Khuyến Mãi</label>
                    <input type="number" class="form-control" name="variants[${variantIndex}][price_sale]">
                </div>
                <div class="form-group">
                    <label for="variant_stock">Số Lượng</label>
                    <input type="number" class="form-control" name="variants[${variantIndex}][stock]" required>
                </div>
                <div class="form-group">
                    <label for="attribute_name">Tên biến thể</label>
                    <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][name]" required>
                </div>
                <div class="form-group">
                    <label for="attribute_value">Giá trị biến thể</label>
                    <input type="text" class="form-control" name="variants[${variantIndex}][attributes][0][value]" required>
                </div>
            <div class="form-group">
    <label for="variant_images">Hình Ảnh Biến Thể</label>
    <input type="file" class="form-control" name="variants[${variantIndex}][images][]" multiple>
    @error('variants.*.images')
        <div class="alert alert-danger mt-2">{{ $message }}</div>
    @enderror
</div>

            `;

            variantContainer.appendChild(variantDiv);
            variantIndex++;
        });
    });
    </script>
@endsection
