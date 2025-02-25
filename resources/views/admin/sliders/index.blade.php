@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-center mt-3 mb-4">📋 Danh sách Sliders</h2>

    <!-- Nút Thêm Slider -->
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.sliders.create') }}" class="btn btn-success">
            ➕ Thêm Slider
        </a>
    </div>

    <!-- Hiển thị thông báo -->
    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <!-- Bảng danh sách sliders -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped text-center" style="border: 2px solid black;">
            <thead class="table-dark">
                <tr style="border: 2px solid black;">
                    <th style="border: 2px solid black;">ID</th>
                    <th style="border: 2px solid black;">Hình ảnh</th>
                    <th style="border: 2px solid black;">Chú thích</th>
                    <th style="border: 2px solid black;">Liên kết</th>
                    <th style="border: 2px solid black;">Vị trí</th>
                    <th style="border: 2px solid black;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sliders as $slider)
                <tr style="border: 2px solid black;">
                    <td style="border: 2px solid black;">{{ $slider->slider_id }}</td>
                    <td style="border: 2px solid black;">
                        <img src="{{ asset($slider->image_url) }}" class="border rounded" width="100" style="border: 2px solid black;">
                    </td>
                    <td style="border: 2px solid black;">{{ $slider->caption }}</td>
                    <td style="border: 2px solid black;">
                        <a href="{{ $slider->link }}" target="_blank" class="text-decoration-none">
                            🌍 Xem liên kết
                        </a>
                    </td>
                    <td style="border: 2px solid black;">
                        <span class="badge bg-primary">{{ $slider->position }}</span>
                    </td>
                    <td style="border: 2px solid black;">
                        <a href="{{ route('admin.sliders.show', $slider->slider_id) }}" class="btn btn-info btn-sm">👀 Xem</a>
                        <a href="{{ route('admin.sliders.edit', $slider->slider_id) }}" class="btn btn-warning btn-sm">✏️ Sửa</a>
                        <form action="{{ route('admin.sliders.destroy', $slider->slider_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                🗑️ Xóa
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
