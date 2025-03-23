@extends('admin.layout')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Sửa Thuộc Tính</h1>

        <form action="{{ route('variant_attributes.update', [ $variantAttribute->attribute_id]) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div>
                <label for="attribute_name" class="block text-sm font-medium">Tên Thuộc Tính</label>
                <input type="text" id="attribute_name" name="attribute_name"
                    class="text-black w-full p-2 border rounded-lg @error('attribute_name') border-red-500 @enderror" value="{{ $variantAttribute->attribute_name }}">
                @error('attribute_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attribute_value" class="block text-sm font-medium">Giá trị Thuộc Tính</label>
                <input type="text" id="attribute_value" name="attribute_value"
                    class="text-black w-full p-2 border rounded-lg @error('attribute_value') border-red-500 @enderror" value="{{ $variantAttribute->attribute_value }}">
                @error('attribute_value')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <button type="submit" class="btn btn-success mt-2">Cập Nhật</button>
            <a href="{{ route('variant_attributes.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
        </form>
    </div>
@endsection
