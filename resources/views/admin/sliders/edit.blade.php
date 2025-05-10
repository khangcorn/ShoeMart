@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Chỉnh sửa Slider</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sliders.update', $slider->slider_id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">URL Hình ảnh</label>
            <input type="text" name="image_url" class="form-control" value="{{ $slider->image_url }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Chú thích</label>
            <textarea name="caption" class="form-control">{{ $slider->caption }}</textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Liên kết</label>
            <input type="text" name="link" class="form-control" value="{{ $slider->link }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Vị trí</label>
            <input type="number" name="position" class="form-control" value="{{ $slider->position }}">
        </div>
        <button type="submit" class="btn btn-success">Cập nhật</button>
        <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
