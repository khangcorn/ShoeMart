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

            <label for="variants">Biến Thể</label>
            <div id="variants">
                <div class="variant">
                    <div class="form-group">
                        <label for="variant_price">Giá</label>
                        <input type="number" class="form-control" name="variants[0][price]" required>
                    </div>
                    <div class="form-group">
                        <label for="variant_price_sale">Giá Khuyến Mãi</label>
                        <input type="number" class="form-control" name="variants[0][price_sale]">
                    </div>
                    <div class="form-group">
                        <label for="variant_stock">Số Lượng</label>
                        <input type="number" class="form-control" name="variants[0][stock]" required>
                    </div>
                    <div class="form-group">
                        <label for="attribute_name">Tên biến thể</label>
                        <input type="text" class="form-control" name="variants[0][attributes][0][name]" required>
                    </div>
                    <div class="form-group">
                        <label for="attribute_value">Giá trị biến thể</label>
                        <input type="text" class="form-control" name="variants[0][attributes][0][value]" required>
                    </div>





                    <button type="submit" class="btn btn-success mt-2">Lưu</button>
                    <a href="{{ route('product_variants.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>

@endsection