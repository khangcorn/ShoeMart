@extends('layout')

@section('content')
<div class="container">
    <h2>Thêm danh mục mới</h2>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Tên danh mục</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Lưu</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
    </form>
</div>
@endsection
