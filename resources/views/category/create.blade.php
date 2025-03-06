@extends('layout')

@section('content')
<div class="container mx-auto p-6">
    <h2 class="text-3xl font-bold mb-6">Thêm danh mục mới</h2>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Tên danh mục</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" >
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <button type="submit" class="btn btn-success mt-2">Lưu</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-2">Quay lại</a>
    </form>
</div>
@endsection
