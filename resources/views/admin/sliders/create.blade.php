@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card p-4 border rounded">
        <h2 class="text-center mb-4">Thêm Slider</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.sliders.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">URL Hình ảnh</label>
                <input type="text" name="image_url" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Chú thích</label>
                <textarea name="caption" class="form-control"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Liên kết</label>
                <input type="text" name="link" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Vị trí</label>
                <input type="number" name="position" class="form-control" value="0">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary px-4">Thêm</button>
                <a href="{{ route('admin.sliders.index') }}" class="btn btn-secondary px-4">Quay lại</a>
            </div>
        </form>
    </div>
</div>
@endsection
