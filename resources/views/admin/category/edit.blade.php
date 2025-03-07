@extends('admin.layout')

@section('content')
<div class="container">
    <h2>Chỉnh sửa danh mục</h2>

    <form action="{{ route('categories.update', $category->category_id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label for="name">Tên danh mục</label>
            <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Cập nhật</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
    </form>
</div>
@endsection
